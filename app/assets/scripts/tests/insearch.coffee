describe "Script principal insearch.js", ->

  describe "Le module des fonctionnalités de base", ->

    it "Devrait permettre de cliquer sur un bouton des panneaux pour ouvrir et fermer un formulaire", ->
      expect($('.option-panel form:hidden').exists()).toBe yes
      $('.option-panel a.btn').trigger 'click'
      expect($('.option-panel form:hidden').exists()).toBe no
      $('.option-panel a.btn').trigger 'click'
      expect($('.option-panel form:hidden, .option-panel form:animated').exists()).toBe yes


    it "Devrait permettre d'ouvrir et fermer des menus déroulants", ->
      $btn = $ '.dropdown-toggle:first'
      id = $btn.attr 'id'
      expect($('[aria-labelledby="' + id + '"]:hidden').exists()).toBe yes
      $btn.trigger 'click'
      expect($('[aria-labelledby="' + id + '"]:hidden').exists()).toBe no
      $btn.trigger 'click'
      expect($('[aria-labelledby="' + id + '"]:hidden, [aria-labelledby="' + id + '"]:animated').exists()).toBe yes


    it "Devrait permettre de modifier le nombre de résultats par page à afficher", ->
      unless $('h1').is '.results'
        $choicePerPage = $ '[aria-labelledby="choice-per-page"]:first'
        $a = $choicePerPage.find 'a[data-value]:first'
        $input = $ 'input[name="resultsPerPage"]'
        $input.val '-1'
        $a.data('value', '20').trigger 'click'
        expect($input.val()).toBe '20'
        $input.val ''


    it "Devrait activer l'autocomplétion sur le champ de name 'q'", ->
      expect($('[name="q"]').parent().find('.autocomplete').exists()).toBe yes


  describe "L'adaptation mobile", ->

    it "Devrait détecter la présence de tiret dans les titres et créer un span de class 'mobile-hidden' s'il y en a", ->
      expect($('h1').exists() and $('h1').html().indexOf('-') isnt -1).toBe $('mobile-hidden').exists()


    it "Devrait agrandir/réduire les blocs en fonction des dimensions", ->
      $containers = $ 'body, #wrap, #footer, #wrap .container, .navbar'
      $containers.width 800
      $(window).trigger 'resize'
      expect($('.navbar-inner .input-group.input-group-sm').length).toBe 0
      expect($('.navbar-inner .btn-group.btn-group-sm').length).toBe 0
      expect($('.mobile-hidden').length).toBe 0
      expect(parseInt $('h1').css('font-size')).toBeGreaterThan 24
      $containers.width 600
      $(window).trigger 'resize'
      expect($('.navbar-inner .input-group.input-group-sm').length).toBe $('.navbar-inner .input-group').length
      expect($('.navbar-inner .btn-group.btn-group-sm').length).toBe $('.navbar-inner .btn-group').length
      expect($('.mobile-hidden').length).toBe 0
      expect(parseInt $('h1').css('font-size')).toBeGreaterThan 24
      $containers.width 200
      $(window).trigger 'resize'
      expect($('.navbar-inner .input-group.input-group-sm').length).toBe $('.navbar-inner .input-group').length
      expect($('.navbar-inner .btn-group.btn-group-sm').length).toBe $('.navbar-inner .btn-group').length
      expect($('.mobile-hidden').length).toBe $('.mobile-hidden:hidden').length
      expect(parseInt $('h1').css('font-size')).toBeLessThan 24
      expect($('.navbar-inner .btn-group').css('float')).toBe 'right'
      $containers.width 'auto'
      $(window).trigger 'resize'
