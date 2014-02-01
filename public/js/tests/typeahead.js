
describe("Plug-in typeahead", function() {

  describe("La méthode exists", function() {

    it("Devrait être définie", function() {
      expect($('body').exists).toBeDefined();
    });

    it("Devrait permettre de savoir si une collection jQuery contient au moins 1 élément", function() {
      expect($('body').exists()).toBe(true);
    });

    it("Devrait permettre de savoir si une collection jQuery ne contient aucun élément", function()  {
      expect($('#id-qui-nexiste-pas').exists()).toBe(false);
    });

  });

  describe("La méthode autocomplete", function() {

    it("Devrait être définie", function() {
      expect($('input').autocomplete).toBeDefined();
    });

    it("Devrait permettre l'autocompletion d'un champ", function() {
      findOrCreate('input[type="text"]', '<input type="text">', function () {
        var catchValue, catchCallback;
        this.autocomplete(function (value, callback) {
        	catchValue = value;
        	catchCallback = callback;
        });
        this.val('test').trigger('change');
        expect(catchValue).toBe('test');
        expect(typeof(catchCallback)).toBe('function');
        this.val('').trigger('change');
      });
    });

    it("Devrait annuler la propagation des événements lors de l'appui sur Haut et Bas dans un champ", function() {
      findOrCreate('input[type="text"]', '<input type="text">', function () {
        this.autocomplete(function () {});
        var event = jQuery.Event('keydown');
        event.keyCode = 38;
        this.val('test').trigger(event);
        expect(event.isPropagationStopped()).toBe(true);
        var event = jQuery.Event('keydown');
        event.keyCode = 40;
        this.val('').trigger(event);
        expect(event.isPropagationStopped()).toBe(true);
      });
    });

    it("Ne devrait pas empêcher d'écrire dans le champ", function() {
      findOrCreate('input[type="text"]', '<input type="text">', function () {
        this.autocomplete(function () {});
        var event = jQuery.Event('keydown');
        event.keyCode = 65;
        this.val('test').trigger(event);
        expect(event.isPropagationStopped()).toBe(false);
      });
    });

    it("Devrait créer une balise de classe 'autocomplete'", function() {
      findOrCreate('input[type="text"]', '<input type="text">', function () {
        this.autocomplete(function () {});
        expect($('.autocomplete').exists()).toBe(true);
      });
    });

  });

});
