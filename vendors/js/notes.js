laboratree.notes = {};
laboratree.notes.list = {};
laboratree.notes.editor = {};
laboratree.notes.render = {};
laboratree.notes.view = {};
laboratree.notes.masks = {};

/* Plugin Functions */
/* Dashboard Functions */
laboratree.notes.makePortlet = function(data_url) {
	laboratree.notes.portlet = new laboratree.notes.Portlet(data_url);
}

laboratree.notes.Portlet = function(data_url) {
	Ext.QuickTips.init();

	Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
		expires: new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 7))
	}));

	this.data_url = data_url;

	this.state_id = 'state-' + laboratree.context.table_type + '-dashboard-' + laboratree.context.table_id;

	this.column = 'dashboard-column-left';
	this.position = 2;

	this.store = new Ext.data.JsonStore({
		root: 'notes',
		autoLoad: true,
		url: data_url,
		baseParams: {
			model: 'notes'
		},
		fields: ['id', 'title', 'created', 'modified']
	});

	this.portlet = new Ext.grid.GridPanel({
		id: 'portlet-notes',
		height: 200,
		stripeRows: true,
		loadMask: {msg: 'Loading...'},

		store: this.store,

		autoExpandColumn: 'title',

		cm: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true
			},

			columns: [{
				id: 'title',
				header: 'Title',
				dataIndex: 'title',
				renderer: this.renderTitle,
			}]
		})
	});

	this.panel = {
		id: 'panel-notes',
		title: 'Notes',
		layout: 'fit',

		tools: [{
			id: 'help',
			qtip: 'Help Notes',
			handler: function(event, toolEl, panel, tc) {
				Ext.Ajax.request({
					// TODO: Fix Url
					url: String.format(laboratree.links.help.site.index, laboratree.context.table_type, 'notes') + '.json',
					success: function(response, request) {
						var data = Ext.decode(response.responseText);
						if(data.success) {
							laboratree.helpPopup('Notes Help', data.help.Help.content);
						}
					},
					failure: function() {
					}
				});
			}
		}],

		items: this.portlet,

		listeners: {
			expand: function(p) {
				laboratree.notes.portlet.toggle(false);
			},
			collapse: function(p) {
				laboratree.notes.portlet.toggle(true);
			}
		}
	};

	if(laboratree.site.permissions.notes.view & laboratree.context.permissions.note) {
		this.panel.title = '<a href="' + String.format(laboratree.links.notes.articles, laboratree.context.table_type, laboratree.context.table_id) + '">Notes</a>';

		this.panel.tools.push({
			id: 'restore',
			qtip: 'Notes Dashboard',
			handler: function() {
				window.location = String.format(laboratree.links.notes.laboratree.context.table_type, laboratree.context.table_id);
			}
		});
	}

	if(laboratree.site.permissions.notes.add & laboratree.context.permissions.note) {
		this.panel.title += '<span class="create-link">';
		this.panel.title += '<a href="' + String.format(laboratree.links.notes.add, laboratree.context.table_type, laboratree.context.table_id) + '">- add note -</a>';
		this.panel.title += '</span>';

		this.panel.notes.tools.unshift({
			id: 'plus',
			qtip: 'Add a Note',
			handler: function() {
				window.location = String.format(laboratree.links.notes.add, laboratree.context.table_type, laboratree.context.table_id);
			}	
		});
	}

	var states = Ext.state.Manager.get(this.state_id, null);
	if(!states) {
		states = {};
	}

	var state = states.notes;
	if(!state) {
		state = {
			collapsed: false,
			column: this.column,
			position: this.position
		};
	}

	this.panel.collapsed = state.collapsed;

	var column = Ext.getCmp(state.column);
	if(!column) {
		return false;
	}

	column.insert(state.position, this.panel);
};

laboratree.notes.Portlet.prototype.renderTitle = function(value, p, record) {
	var permission = laboratree.context.permissions.notes;
	if(record.data.permission && record.data.permission.notes) {
		permission = parseInt(record.data.permission.notes, 10);
	}

	var label = value;
	if(laboratree.site.permissions.notes.view & permission) {
		label = String.format('<a href="' + laboratree.links.notes.view + '" title="{1}">{1}</a>', record.data.id, value);
	}

	return label;
};

laboratree.notes.Portlet.prototype.toggle = function(collapsed) {
	var states = Ext.state.Manager.get(this.state_id, null);
	if(!states) {
		states = {};
	}

	var state = states.notes;
	if(!state) {
		state = {
			collapsed: false,
			column: this.column,
			position: this.position
		};

		states.notes = state;
	}

	states.notes.collapsed = collapsed;

	Ext.state.Manager.set(this.state_id, states);
};

laboratree.notes.makeList = function(title, div, data_url, table_type, table_id) {
	laboratree.notes.list = new laboratree.notes.List(title, div, data_url, table_type, table_id);
};

laboratree.notes.List = function(title, div, data_url, table_type, table_id) {
	Ext.QuickTips.init();

	this.table_type = table_type;
	this.table_id = table_id;

	this.store = new Ext.data.JsonStore({
		root: 'articles',
		autoLoad: true,
		url: data_url,
		fields: [
			'id', 'title', 'content', 'created', 'permanent'
		]
	});

	this.store.setDefaultSort('created', 'DESC');

	var gridConfig = {
		id: 'notes',
		title: title,
		renderTo: div,
		width: '100%',
		height: 600,
		stripeRows: true,

		store: this.store,

		cm: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true
			},
			columns: [{
				id: 'title',
				header: 'Title',
				dataIndex: 'title',
				width: 800,
				renderer: laboratree.notes.render.title
			},{
				id: 'actions',
				header: 'Actions',
				dataIndex: 'id',
				width: 140,
				align: 'center',
				renderer: laboratree.notes.render.actions
			}]
		}),

		tools: [{
			id: 'refresh',
			qtip: 'Refresh Notes',
			handler: function(event, toolEl, panel, tc) {
				panel.store.reload();	
			}
		}],

		bbar: new Ext.PagingToolbar({
			pageSize: 30,
			store: this.store,
			displayInfo: true,
			displayMsg: 'Displaying notes {0} - {1} of {2}',
			emptyMsg: 'No notes to display'
		})
	};

	if(laboratree.site.permissions.notes.add & laboratree.context.permissions.note)
	{
		gridConfig.tools.unshift({
			id: 'plus',
			qtip: 'Add a Note',
			handler: function() {
				window.location = String.format(laboratree.links.notes.add, table_type, table_id);
			}
		});
	}

	this.grid = new Ext.grid.GridPanel(gridConfig);
};

laboratree.notes.makeAdd = function(div, data_url) {
	Ext.onReady(function(){
		laboratree.notes.add = new laboratree.notes.Add(div, data_url);
	}, this);
};

laboratree.notes.Add = function(div, data_url) {
	Ext.QuickTips.init();

	this.div = div;
	this.data_url = data_url;

	this.editor = new Ext.form.HtmlEditor({
		id: 'NoteContent',
		width: '100%',
		height: 460,
		fieldLabel: 'Content',
		name: 'data[Note][content]'
	});
	
	this.form = new Ext.form.FormPanel({
		title: 'Notes',
		renderTo: div,
		width: '100%',
		height: 600,
		standardSubmit: true,
		frame: true,

		labelAlign: 'top',
		
		buttonAlign: 'center',
		defaultType: 'textfield',
		
		defaults: {
			anchor: '100%'
		},
		
		items: [{
			id: 'NoteTitle',
			fieldLabel: 'Title',
			name: 'data[Note][title]',
			vtype: 'noteTitle'
		}, this.editor],
		
		buttons: [{
			text: 'Save',
			data_url: data_url,
			handler: function(button, e) {
				if(laboratree.notes.add.form.getForm().isValid()) {
					laboratree.notes.add.form.getForm().submit({
						url: data_url
					});
				}
			}
		}]
	});
};

laboratree.notes.makeEdit = function(div, data_url) {
	Ext.onReady(function(){
		laboratree.notes.edit = new laboratree.notes.Edit(div, data_url);

		laboratree.notes.masks.edit = new Ext.LoadMask('edit', {
			msg: 'Loading...'
		});
		laboratree.notes.masks.edit.show();

		Ext.Ajax.request({
			url: data_url + '.json',
			success: function(response, request) {
				var data = Ext.decode(response.responseText);
				
				if(!data) {
					request.failure(response, request);
					return;
				}

				if(data.error) {
					request.failure(response, request);
					return;
				}
				
				var title = Ext.getCmp('NoteTitle');
				if(title) {
					title.setValue(data.title);
				}
			
				var content = Ext.getCmp('NoteContent');
				if(content) {
					content.setValue(data.content);
				}

				laboratree.notes.masks.edit.hide();
			},
			failure: function(response, request) {
				laboratree.notes.masks.edit.hide();
			}
		});
	});
};

laboratree.notes.Edit = function(div, data_url) {
	Ext.QuickTips.init();

	this.div = div;
	this.data_url = data_url;

	this.editor = new Ext.form.HtmlEditor({
		id: 'NoteContent',
		width: '100%',
		height: 460,
		fieldLabel: 'Content',
		name: 'data[Note][content]'
	});

	this.form = new Ext.form.FormPanel({
		id: 'edit',
		title: 'Notes',
		renderTo: div,
		width: '100%',
		height: 600,
		standardSubmit: true,
		frame: true,

		labelAlign: 'top',
		
		buttonAlign: 'center',
		defaultType: 'textfield',
		
		defaults: {
			anchor: '100%'
		},
		
		items: [{
			id: 'NoteTitle',
			fieldLabel: 'Title',
			name: 'data[Note][title]',
			vtype: 'noteTitle'
		}, this.editor],
		
		buttons: [{
			text: 'Save',
			data_url: data_url,
			handler: function() {
				if(laboratree.notes.edit.form.getForm().isValid()) {
					laboratree.notes.edit.form.getForm().submit({
						url: data_url
					});
				}
			}
		}]
	});
};

laboratree.notes.List.prototype.remove = function(article_id) {
	if(window.confirm('Are you sure?')) {
		Ext.Ajax.request({
			url: String.format(laboratree.links.notes['delete'], article_id) + '.json',
			success: function(response, request) {
				var data = Ext.decode(response.responseText);
				if(data.success) {
					var record = laboratree.notes.list.store.getById(article_id);
					if(record) {
						laboratree.notes.list.store.remove(record);
					}
				}
			}
		});
	}
};

laboratree.notes.render.title = function(value, p, record) {
	return String.format('<a href="' + laboratree.links.notes.view + '" title="{1}">{1}</a>', record.id, value);
};

laboratree.notes.render.user = function(value, p, record) {
	return String.format('<a href="' + laboratree.links.users.profile + '" title="{1}">{1}</a>', record.data.user_id, value);
};

laboratree.notes.render.actions = function(value, p, record) {
	var permission = 0;
	if(record.data.permission && record.data.permission.note) {
		permission = parseInt(record.data.permission.note);
	}

	var actions = '';

	if(laboratree.site.permissions.notes.edit & permission) {
		actions += String.format('<a href="' + laboratree.links.notes.edit + '" title="Edit {1}">Edit</a>', value, record.data.title);
	}

	if(record.data.permanent == '0' && (laboratree.site.permissions.notes['delete'] & permission))
	{
		if(actions != '') {
			actions += '&nbsp;|&nbsp;';
		}

		actions += String.format('<a href="#" onclick="laboratree.notes.list.remove({0}); return false;" title="Delete {1}">Delete</a>', value, record.data.title);
	}

	return actions;
};

laboratree.notes.makeView = function(div, article_id, data_url) {
	laboratree.notes.view = new laboratree.notes.View(div, article_id, data_url);
};

laboratree.notes.View = function(div, article_id, data_url) {
	this.div = div;
	this.article_id = article_id;
	Ext.QuickTips.init();

	Ext.Ajax.request({
		url: data_url,
		success: function(response, request) {
			var data = Ext.decode(response.responseText);
			if(!data)
			{
				request.failure(response, request);
				return;
			}

			if(data.error || data.errors)
			{
				request.failure(response, request);
				return;
			}
			
			this.panel = new Ext.Panel({
				id: 'note-view',
				renderTo: this.div,
				frame: true,
				width: '100%',
				title: data.title,
				autoHeight: true,
				anchor: '100% 100%',
				bodyStyle: 'padding: 0;',
			
				store: this.store,				
	
				items: [{
					//width: '100%',
					
					anchor: '100% 100%',
					html: data.content,
					bodyStyle: 'background-color: #ffffff; padding: 5px; border: 1px solid #999999;'
				}]
			});

			this.panel.doLayout();

		},
		failure: function(response, request) {

		},
		scope: this
	});
};
