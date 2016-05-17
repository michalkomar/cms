/**
 *
 * @param {object} settings
 * Settings contains:<br>
 * - createUrl (automatic adding create button to grid)<br>
 * - readUrl<br>
 * - updateUrl (automatic adding update button to grid)<br>
 * - destroyUrl (automatic adding delete button to grid)<br>
 * - node // css selector of element<br>
 * - colums // kendo columns settings for grid<br>
 * - dataSourceSchema // kendo dataSource schema settings<br>
 * - component {string} grid (next coming soon)<br>
 * - editMode {string|false} inline || popup || custom-popup || false<br>
 * -- customPopupModalNode // css selector of bootstrap modal window
 * - gridType parent | detail
 * - viewModelExtension // extensions for viewModel e.g. events
 * - onDialogOpen function This event fires immediately when the show instance method is called
 * - onDialogClose function This event is fired immediately when the hide instance method has been called
 *
 * @see http://docs.telerik.com/kendo-ui/api/javascript/ui/grid#configuration-columns
 * @see http://docs.telerik.com/kendo-ui/api/javascript/data/datasource#configuration-schema
 * @returns {kendoCRM}
 */
var kendoCRM = function (settings){
	var that = this;
	this.settings = settings;

	this.createUrl = settings.createUrl;
	this.readUrl = settings.readUrl;
	this.updateUrl = settings.updateUrl;
	this.destroyUrl = settings.destroyUrl;

	this.node = settings.node;
	this.columns = settings.columns;
	this.dataSourceSchema = typeof settings.dataSourceSchema === 'undefined' ? {} : settings.dataSourceSchema;
	this.component = settings.component;

	this.pageable = {
		buttonCount: 5
	};

	this.defaultSorting = {};
	if (typeof settings.defaultSorting === 'object')
	{
		this.defaultSorting = $.extend(this.defaultSorting, settings.defaultSorting);
	}

	if (typeof this.settings.gridType === 'undefined')
	{
		this.settings.gridType = 'parent';
	}
	if (this.settings.gridType === 'child' && typeof this.settings.detailRow === 'undefined')
	{
		alert('For detail grid must be set detailRow');
	}
	if (this.settings.gridType === 'child' && typeof this.settings.detailInit === 'undefined')
	{
		alert('For detail grid must be set detailInit');
	}

	this.editable = {};

	/**
	 * inline || popup || custom-popup || false
	 */
	this.editMode = 'inline';

	if (typeof settings.editMode === 'undefined')
	{
		this.editMode = 'inline';
	}
	else { this.editMode = settings.editMode; }

	if (typeof this.settings.dataSource === 'undefined')
	{
		this.dataSource = this.prepareDataSource();
	}
	else
	{
		this.dataSource = this.settings.dataSource;
	}
	this.prepareViewModel();

	switch (this.editMode) {
		case 'inline':
			this.editable.mode = 'inline';
			break;
		case 'popup':
			this.editable.mode = 'popup';
			break;
		case 'custom-popup':
			this.editable = false;
			break;
		case false:
			this.editable = false;
			break;
		default:
			alert('Edit mode (settings.editMode) can have only following values: inline, popup, custom-popup or false. Default is inline. \nYour setting is ' + this.editMode + '.');
	};

	if (this.editMode === 'custom-popup' && typeof settings.customPopupModalNode === 'undefined')
	{
		alert('For custom-popup edit must be defined settings.customPopupModalNode property. Use bootstrap modal node.');
	}
	else if (this.editMode === 'custom-popup' && typeof settings.customPopupModalNode === 'string')
	{
		this.customPopupModalNode = settings.customPopupModalNode;
		this.customPopupModalObject = $(this.customPopupModalNode);

		kendo.bind(this.customPopupModalObject, this.viewModel);

	}

	if (this.component === 'grid')
	{
		this.createGrid();
	}

	if (this.editMode === 'custom-popup' && typeof this.settings.onDialogOpen === 'function')
	{
		$(this.customPopupModalNode).on('show.bs.modal', function(e){
			that.settings.onDialogOpen(e, that);
		});
	}

	if (this.editMode === 'custom-popup' && typeof this.settings.onDialogClose === 'function')
	{
		$(this.customPopupModalNode).on('hide.bs.modal', function(e){
			that.settings.onDialogClose(e, that);
		});
	}

	if (this.settings.autoRefresh)
	{
		setTimeout(this.autoRefresh, 10000, this);
	}

	return this.viewModel;
};

/**
 * @returns {kendo.observable}
 */
kendoCRM.prototype.prepareViewModel = function(){
	var that = this;
	var viewModel = kendo.observable({
		dataSource: this.dataSource,
		selected: {},
		hasChanges: false,
		getCustomPopup: function()
		{
			return that.customPopupModalObject;
		},
		successCallback: function(){
			console.log('emptySuccessCallback');
		},
		save: function(e){
//			if(that.validator.validate()) {
				this.successCallback = null;
				if (that.updateUrl === true)
				{
					that.grid.refresh();
				}
				else
				{
					this.dataSource.sync();
				}
//			};
		},
		saveAndClose: function(e){
//			if(that.validator.validate()) {
				this.successCallback = this.closeDialog;
				this.dataSource.sync();
//			};
		},
		cancel: function (e){
			if (that.dataSource.hasChanges() && that.updateUrl !== true && that.createUrl !== true)
			{
				that.dataSource.cancelChanges();
			}
			this.closeDialog();
		},
		openDialog: function(){
			$(that.customPopupModalNode).modal('show');
		},
		closeDialog: function(){
			$(that.customPopupModalNode).modal('hide');
		}
	});

	if (typeof this.settings.viewModelExtension === 'object')
	{
		$.extend(viewModel, this.settings.viewModelExtension);
	}

	this.viewModel = viewModel;
};

/**
 * Create kendo dataSource
 * @returns {kendo.data.DataSource}
 */
kendoCRM.prototype.prepareDataSource = function(){
	var that = this;


	var dataSourceSetting = {
		transport: {
			dataType: 'jsonp'
		},
		pageSize: 50,
		change: function(changeEvent){
			that.viewModel.set("hasChanges", this.hasChanges());
			if (this.hasChanges())
			{
				$(that.customPopupModalNode).find('button').switchClass('btn-disable', 'btn-success');
			}
			else
			{
				$(that.customPopupModalNode).find('button').switchClass('btn-success', 'btn-disable');
			}
		},
		sync: function(e){
			var model = that.dataSource.get(that.viewModel.get('selected').id);
			that.viewModel.set('selected', model);
		},
		schema: {},
		sort: this.defaultSorting
	};

	if (typeof this.createUrl === 'string')
	{
		dataSourceSetting.transport.create = {
			url: this.createUrl,
			type: 'POST',
			complete: function(jqXHR){
				that.transportComplete(jqXHR, 'Uložení nového záznamu proběhlo úspěšně.', 'Při ukládání došlo k chybě.', 'create');
			}
		};
	}
	if (typeof this.readUrl === 'string')
	{
		dataSourceSetting.transport.read = {
			url: this.readUrl,
			type: 'POST'
		};
	}
	if (typeof this.updateUrl === 'string')
	{
		dataSourceSetting.transport.update = {
			url: this.updateUrl,
			type: 'POST',
			complete: function(jqXHR){
				that.transportComplete(jqXHR, 'Úprava záznamu proběhla úspěšně.', 'Při úpravě záznamu došlo k chybě.', 'update');
			}
		};
	}
	if (typeof this.destroyUrl === 'string')
	{
		dataSourceSetting.transport.destroy = {
			url: this.destroyUrl,
			type: 'POST',
			complete: function(jqXHR){
				that.transportComplete(jqXHR, 'Smazání záznamu proběhlo úspěšně.', 'Při mazání záznamu došlo k chybě.', 'destroy');
			}
		};
	}

	$.extend(dataSourceSetting.schema, this.dataSourceSchema);
	$.extend(dataSourceSetting, this.settings.dataSourceExtension);

	return new kendo.data.DataSource(dataSourceSetting);
};

kendoCRM.prototype.kendoCRM = function(){};

/**
 * Create kendo grid
 * @returns {kendoGrid}
 */
kendoCRM.prototype.createGrid = function(){
	var that = this;

	if (this.settings.gridType === 'parent')
	{
		this.grid = $(this.node).kendoGrid(this.prepareGridSettings()).data('kendoGrid');
	}
	else if (this.settings.gridType === 'detail')
	{
		this.grid = this.settings.detailRow.find('.content').kendoGrid(this.prepareGridSettings('detail'));
	}
	else
	{
		alert('Invalid grid Type, grid type must be parent, or detail.');
	}

	if (this.editMode === 'custom-popup')
	{
//		$(this.grid.element).find('.k-grid-add').bind('click', function(){
//			var newItem = that.viewModel.dataSource.insert();
//			that.viewModel.set('selected', that.viewModel.dataSource.getByUid(newItem.uid));
//			$(that.customPopupModalNode).modal('show');
//		});
//		this.validator = $(this.customPopupModalNode).kendoValidator({
//			errorTemplate: '<div class="tooltip top" role="tooltip" style="opacity: 1;"><div class="tooltip-inner">#=message#</div></div>'
//		}).data("kendoValidator");
	}
};

kendoCRM.prototype.prepareGridSettings = function(gridType)
{
	if (typeof gridType === 'undefined')
	{
		gridType = 'parentGrid';
	}
	this.setColumns(this.settings);

	var that = this;
	var gridSetting = {
		dataSource: this.dataSource,
		pageable: {
			buttonCount: this.pageable.buttonCount
		},
		sortable: {
			mode: "multiple",
			allowUnsort: true
		},
		filterable: {
			extra: false
		},
		toolbar: this.prepareTopToolbar(),
		columns: this.columns,
		editable: that.editable,
		batch: false,
		selectable: false,
		edit: function(e) {
			var commandCell = e.container.find("td:nth-child(1)");
			commandCell.html('<a class="btn btn-success k-grid-update" style="margin-bottom: 5px;" data-toggle="tooltip" data-placement="right" title="Uložit změny"><span class="glyphicon glyphicon-floppy-saved"></span></a><br /><a class="btn btn-danger k-grid-cancel" data-toggle="tooltip" data-placement="right" title="Zahodit změny"><span class="glyphicon glyphicon-floppy-remove"></span></a>');
			$('[data-toggle="tooltip"]').tooltip();
		},
		change: function(e){
			var model = this.dataItem(this.select());
//            validator.hideMessages();
            that.viewModel.set("selected", model);
		},
		dataBound: function(e) {
			var dataBoundScoupe = this;
			if (that.editMode === 'custom-popup')
			{
				$(e.sender.wrapper).find('.k-grid-edit').on('click', function(e){
					var uid = $(e.currentTarget).closest('tr[role=row]').data('uid');
					var row = dataBoundScoupe.tbody.find(">tr[data-uid=" + uid + "]");
					var model = that.viewModel.dataSource.getByUid(uid);
					that.viewModel.set("selected", model);
					that.viewModel.openDialog();
				});

				$(e.sender.wrapper).find('.k-grid-add').unbind('click');
				$(e.sender.wrapper).find('.k-grid-add').on('click', function(e){
					var model = that.viewModel.dataSource.add();
					that.viewModel.set("selected", model);
					that.viewModel.openDialog();
				});
			}


			$('[data-role="delete"]').bind('click', function(e){
				e.preventDefault();

				var href = $(this).attr('href');
				if (!$('#dataConfirmModal').length) {
					$('body').append('\
							<div id="dataConfirmModal" class="modal fade" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true">\n\
								<div class="modal-dialog">\n\
									<div class="modal-content">\n\
										<div class="modal-header">\n\
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>\n\
											<h4 class="modal-title">Prosím potvrďte</h4>\n\
										</div>\n\
										<div class="modal-body">Opravdu chcete smazat položku <strong class="name"></strong>?</div>\n\
										<div class="modal-footer">\n\
											<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Zrušit</button>\n\
											<a class="btn btn-danger" data-role="accept"><i class="glyphicon glyphicon-trash"></i> Smazat</a>\n\
										</div>\n\
									</div>\n\
								</div>\n\
							</div>'
							);
				}
				var confirm = $('#dataConfirmModal');

				kendo.bind(confirm, that.viewModel);

				var uid = $(e.currentTarget).closest('tr[role=row]').data('uid');
				var model = that.viewModel.dataSource.getByUid(uid);

				that.viewModel.set("selected", model);

				confirm.find('.modal-body strong.name').text(model.name);
				confirm.find('[data-role="accept"]').unbind('click');
				confirm.find('[data-role="accept"]').bind('click', function(){
					that.viewModel.dataSource.remove(model);
					that.viewModel.dataSource.sync();
					confirm.modal('hide');
				});
				confirm.modal('show');
			});

			if (typeof that.settings.detailInit === 'function')
			{
				var expandRow = function(dataBoundScoupe) {
					element = $("tr.k-master-row a[data-workplace-id='" + window.location.hash.substr(1) + "']").parent().parent();

					if (element.length !== 0)
					{
						dataBoundScoupe.collapseRow('tr.k-master-row');
						dataBoundScoupe.expandRow(element);

						$('html, body').animate({
								scrollTop: $(element).offset().top
						}, 2000);
						}
				};
				$(window).on('hashchange', dataBoundScoupe, function(then) {
					expandRow(then.data);
				});
				expandRow(dataBoundScoupe);

				$('.k-hierarchy-cell .k-icon.k-plus').addClass('btn btn-success').removeClass('k-icon').append(
					'<span class="glyphicon glyphicon-expand"></span>'
				);
			}

			$('[data-toggle="popover"]').popover();
			$('[data-toggle="tooltip"]').tooltip();

			if (typeof that.settings.dataBoundExtension === 'function')
			{
				that.settings.dataBoundExtension(that, e);
			}
		}
	};

	if (typeof this.settings.detailInit !== 'undefined')
	{
		$.extend(gridSetting, {
			detailCollapse: function(e){
			$(e.masterRow).find('.k-hierarchy-cell .k-plus span').addClass('glyphicon-expand').removeClass('glyphicon-collapse-up');
			},
			detailExpand: function(e){
				this.collapseRow(this.tbody.find(' > tr.k-master-row').not(e.masterRow));
				$(e.masterRow).find('.k-hierarchy-cell .k-minus span').addClass('glyphicon-collapse-up').removeClass('glyphicon-expand');
			},
			detailTemplate: that.settings.detailTemplate
		});

		if (typeof this.settings.detailInit === "function")
		{
			$.extend(gridSetting, {
				detailInit: this.settings.detailInit
			});
		}
	}

	return gridSetting;
};

/**
 * Set grid columns
 * @param {object} settings
 * @returns {void}
 */
kendoCRM.prototype.setColumns = function(settings){
	if (typeof settings.columns === 'object')
	{
		this.columns = settings.columns;
	}
	else
	{
		this.columns = [];
	}

	if (this.editMode !== false && (typeof this.updateUrl === 'string' || this.updateUrl === true))
	{
		this.columns.unshift(
			{
				command: {
					name: 'edit',
					template: '<a class="btn btn-warning k-grid-edit small" data-toggle="tooltip" data-placement="right" title="Upravit"><span class="glyphicon glyphicon-edit"></span></a>',
					text: {
						edit: 'Upravit',
						update: 'Uložit',
						cancel: 'Zrušit'
					}
				},
				attributes: {
					"class": 'pdf-export-hide'
				},
				title: 'Upravit',
				width: 75
			}
		);
	}
	if (typeof this.destroyUrl === 'string' || this.destroyUrl === true)
	{
		this.columns.push(
			{
				command: {
					name: 'destroy',
					template: '&nbsp;<a class="btn btn-danger" data-role="delete" data-toggle="tooltip" data-placement="left" title="Smazat"><span class="glyphicon glyphicon-trash"></span></a>'
				},
				title: 'Smazat',
				width: 65
			}
		);
	}
};

/**
 *
 * @returns {Array|kendoCRM.prototype.prepareTopToolbar.toolbarCommands|Boolean}
 */
kendoCRM.prototype.prepareTopToolbar = function(){
	var toolbarCommands = [];
	var addButtonText = (typeof this.settings.addButtonText !== 'undefined') ? this.settings.addButtonText : 'Přidat položku';
	if (typeof this.createUrl === 'string' || this.createUrl === true)
	{
		toolbarCommands.push({
			name: 'create',
			template: kendo.template('<a class="k-grid-add  btn btn-success btn-sm"><i class="glyphicon glyphicon-plus-sign"></i> '+addButtonText+'</a>')
		});
	}

	if (typeof this.settings.toolbarCommandsExtension !== 'undefined' && $.isArray(this.settings.toolbarCommandsExtension))
	{
		$.each(this.settings.toolbarCommandsExtension, function(index, value){
			toolbarCommands.push(value);
		});
	}
	//toolbarCommands.push("pdf");
	//toolbarCommands.push("excel");

	return toolbarCommands.length === 0 ? false : toolbarCommands;
};

/**
 * Show success flash message or animate modal window
 * @param {string} message
 * @param {string} operation
 * @returns {void}
 */
kendoCRM.prototype.completeSuccess = function(message, operation){
	this.viewModel.hasChanges = false;

	if (this.editMode === 'custom-popup' && operation !== 'destroy')
	{
		this.animateModalContent('#dff0d8');
	}
	else
	{
		bootstrap.flashMessage(message);
	}
	setTimeout(this.viewModel.successCallback, 2000);
};

/**
 * Show error flash message or animating modal vindow
 * @param {string} message
 * @returns {void}
 */
kendoCRM.prototype.completeError = function(message){
	if (this.editMode === 'custom-popup')
	{
		this.animateModalContent('#f2dede');
	}
	bootstrap.flashMessage(message, 'danger');
};

/**
 * Animate bootstrap modal content background
 * @param {string} color
 * @returns {undefined}
 */
kendoCRM.prototype.animateModalContent = function(color)
{
	$('.modal-content').animate({
		backgroundColor: color
	}).delay(1000).animate({ backgroundColor: 'auto'});
};

kendoCRM.prototype.transportComplete = function(jqXHR, successMessage, errorMessage, operation){
	try
	{
		var responseText = $.parseJSON(jqXHR.responseText);
	}
	catch (e)
	{
		var responseText = jqXHR.responseText;
	}

	typeof responseText.redirect === 'string' ? window.location.replace(responseText.redirect) :null;

	if (jqXHR.status === 200)
	{
		this.completeSuccess(successMessage, operation);
	}
	else
	{
		this.completeError(errorMessage + '<br>Chyba byla automaticky <strong>zaznamenána</strong>.', 'operation');
	}
};

kendoCRM.prototype.autoRefresh = function(that) {
	if ($(that.customPopupModalObject).is(':hidden'))
	{
		that.dataSource.read();
	}

	setTimeout(that.autoRefresh, 10000, that);
};
