Class.Define('Admin',{
	Extend: Module,
	Constructor: function () {
		this.parent();
		if (typeof (List) != 'undefined') new List();
	}
});

// run all declared javascripts after <body>, after all elements are declared
window.admin = new Admin();