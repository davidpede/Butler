var Butler = function(config) {
    config = config || {};
    Butler.superclass.constructor.call(this,config);
};
Ext.extend(Butler,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('butler',Butler);
Butler = new Butler();