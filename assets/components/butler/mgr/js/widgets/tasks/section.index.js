// Good reading: C:\xampp\htdocs\repo\revolution\manager\assets\modext\widgets\security\modx.grid.user.js

Ext.onReady(function() {
  MODx.load({ xtype: 'butler-tasks'}); //component xtype to be displayed
});
Butler.tasks = function(config) {
  config = config || {};
  Ext.applyIf(config,{
    components: [{
      xtype: 'butler-panel-tasks' //content xtype this component displays
    }]
  });
  Butler.tasks.superclass.constructor.call(this,config);
};
Ext.extend(Butler.tasks,MODx.Component);
Ext.reg('butler-tasks',Butler.tasks); //register this component to its xtype