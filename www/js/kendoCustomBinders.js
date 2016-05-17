kendo.data.binders.widget.maxDate = kendo.data.Binder.extend( {
	init: function(widget, bindings, options) {
		//call the base constructor
		kendo.data.Binder.fn.init.call(this, widget.element[0], bindings, options);
	},
    refresh: function() {
        var value = this.bindings["maxDate"].get();
		$(this.element).data("kendoDatePicker").max(value);
    }
});

kendo.data.binders.widget.minDate = kendo.data.Binder.extend( {
	init: function(widget, bindings, options) {
		//call the base constructor
		kendo.data.Binder.fn.init.call(this, widget.element[0], bindings, options);
	},
    refresh: function() {
        var value = this.bindings["minDate"].get();
		$(this.element).data("kendoDatePicker").min(value);
    }
});

kendo.data.binders.cssToggle = kendo.data.Binder.extend({

  init: function(element, bindings, options) {

    kendo.data.Binder.fn.
                init.call(
                 this, element, bindings, options
               );

    var target = $(element);
    this.enabledCss = target.data("enabledCss");
    this.disabledCss = target.data("disabledCss");
  },

  refresh: function() {
    if ( this.bindings.cssToggle.get() ) {
      $( this.element ).addClass( this.enabledCss );
      $( this.element ).removeClass( this.disabledCss );
    } else{
      $( this.element ).addClass( this.disabledCss );
      $( this.element ).removeClass( this.enabledCss );
    }
  }

});