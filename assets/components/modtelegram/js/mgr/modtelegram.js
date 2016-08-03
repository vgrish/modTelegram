var modtelegram = function (config) {
	config = config || {};
	modtelegram.superclass.constructor.call(this, config);
};
Ext.extend(modtelegram, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('modtelegram', modtelegram);

modtelegram = new modtelegram();