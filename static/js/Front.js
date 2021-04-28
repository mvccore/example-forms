Class.Define('Front',{
	Extend: Module,
	Constructor: function () {
		this.parent();
		this.initPasswordInputs();
	},
	/** 
	 * @see https://stackoverflow.com/questions/2530/how-do-you-disable-browser-autocomplete-on-web-form-field-input-tag
	 */
	initPasswordInputs: function () {
		var passwordInputs = document.querySelectorAll('input[type=password]');
		for (var i = 0, l = passwordInputs.length; i < l; i++) {
			(function (iLocal) {
				passwordInputs[iLocal].onfocus = function () {
					if (this.hasAttribute('readonly')) {
						this.removeAttribute('readonly');
						this.blur();
						this.focus()
					}
				}
			})(i);
		}
	}
});

if (!console) {
	var console = {
		log: function () { }
	}
}

// run all declared javascripts after <body>, after all elements are declared
window.front = new Front();
