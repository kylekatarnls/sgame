
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
      if(!$('input[type="text"]').exists()) {
      	$('body').append('<input type="text">');
      }
      var catchValue, catchCallback;
      $('input[type="text"]').autocomplete(function (value, callback) {
      	catchValue = value;
      	catchCallback = callback;
      });
      $('input[type="text"]').val('test').trigger('change');
      expect(catchValue).toBe('test');
      expect(typeof(catchCallback)).toBe('function');
    });

    it("Devrait annuler la propagation des événements lors de l'appui sur Haut et Bas dans un champ", function() {
      if(!$('input[type="text"]').exists()) {
      	$('body').append('<input type="text">');
      }
      $('input[type="text"]').autocomplete(function () {});
      var event = jQuery.Event('keydown');
      event.keyCode = 38;
      $('input[type="text"]').val('test').trigger(event);
      expect(event.isPropagationStopped()).toBe(true);
      var event = jQuery.Event('keydown');
      event.keyCode = 40;
      $('input[type="text"]').val('test').trigger(event);
      expect(event.isPropagationStopped()).toBe(true);
    });

    it("Ne devrait pas empêcher d'écrire dans le champ", function() {
      if(!$('input[type="text"]').exists()) {
      	$('body').append('<input type="text">');
      }
      $('input[type="text"]').autocomplete(function () {});
      var event = jQuery.Event('keydown');
      event.keyCode = 65;
      $('input[type="text"]').val('test').trigger(event);
      expect(event.isPropagationStopped()).toBe(false);
    });

    it("Devrait créer une balise de classe 'autocomplete'", function() {
      if(!$('input[type="text"]').exists()) {
      	$('body').append('<input type="text">');
      }
      $('input[type="text"]').autocomplete(function () {});
      expect($('.autocomplete').exists()).toBe(true);
    });

  });

});
