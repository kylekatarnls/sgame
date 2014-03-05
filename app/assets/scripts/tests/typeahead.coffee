
describe "Plug-in typeahead", ->

  describe "La méthode exists", ->

    it "Devrait être définie", ->
      expect($('body').exists).toBeDefined()


    it "Devrait permettre de savoir si une collection jQuery contient au moins 1 élément", ->
      expect($('body').exists()).toBe true


    it "Devrait permettre de savoir si une collection jQuery ne contient aucun élément", ->
      expect($('#id-qui-nexiste-pas').exists()).toBe false


  describe "La méthode autocomplete", ->

    it "Devrait être définie", ->
      expect($('input').autocomplete).toBeDefined()


    it "Devrait permettre l'autocompletion d'un champ", ->
      inTextInput ->
        catchValue = undefined
        catchCallback = undefined
        this.autocomplete (value, callback) ->
          catchValue = value
          catchCallback = callback
        this.val('test').trigger 'change'
        expect(catchValue).toBe 'test'
        expect(typeof catchCallback).toBe 'function'
        this.val('').trigger 'change'


    it "Devrait annuler la propagation des événements lors de l'appui sur Haut et Bas dans un champ", ->
      inTextInput ->
        this.autocomplete ->
        event = jQuery.Event 'keydown'
        event.keyCode = 38
        this.val('test').trigger event
        expect(event.isPropagationStopped()).toBe true
        event = jQuery.Event 'keydown'
        event.keyCode = 40
        this.val('').trigger event
        expect(event.isPropagationStopped()).toBe true


    it "Ne devrait pas empêcher d'écrire dans le champ", ->
      inTextInput ->
        this.autocomplete ->
        event = jQuery.Event 'keydown'
        event.keyCode = 65
        this.val('test').trigger event
        expect(event.isPropagationStopped()).toBe false


    it "Devrait créer une balise de classe 'autocomplete'", ->
      inTextInput ->
        this.autocomplete ->
        expect($('.autocomplete').exists()).toBe true
