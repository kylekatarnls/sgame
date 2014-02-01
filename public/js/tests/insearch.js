describe("Script principal insearch.js", function() {

  describe("Le module des fonctionnalités de base", function() {

    it("Devrait permettre de cliquer sur un bouton des panneaux pour ouvrir et fermer un formulaire", function()  {
      expect($('.option-panel form:hidden').exists()).toBe(true);
      $('.option-panel a.btn').trigger('click');
      expect($('.option-panel form:hidden').exists()).toBe(false);
      $('.option-panel a.btn').trigger('click');
      expect($('.option-panel form:hidden, .option-panel form:animated').exists()).toBe(true);
    });

    it("Devrait permettre d'ouvrir et fermer des menus déroulants", function()  {
      var $btn = $('.dropdown-toggle:first'),
        id = $btn.attr('id');
      expect($('[aria-labelledby="' + id + '"]:hidden').exists()).toBe(true);
      $btn.trigger('click');
      expect($('[aria-labelledby="' + id + '"]:hidden').exists()).toBe(false);
      $btn.trigger('click');
      expect($('[aria-labelledby="' + id + '"]:hidden, [aria-labelledby="' + id + '"]:animated').exists()).toBe(true);
    });

    it("Devrait permettre de modifier le nombre de résultats par page à afficher", function()  {
      if(!$('h1').is('.results')) {
        var $choicePerPage = $('[aria-labelledby="choice-per-page"]:first'),
          $a = $choicePerPage.find('a[data-value]:first'),
          $input = $('input[name="resultsPerPage"]');
        $input.val('-1');
        $a.data('value', '20').trigger('click');
        expect($input.val()).toBe('20');
        $input.val('');
      }
    });

    it("Devrait activer l'autocomplétion sur le champ de name 'q'", function()  {
      expect($('[name="q"]').parent().find('.autocomplete').exists()).toBe(true);
    });

  });

  describe("L'adaptation mobile", function() {

    it("Devrait détecter la présence de tiret dans les titres et créer un span de class 'mobile-hidden' s'il y en a", function() {
      expect($('h1').exists() && $('h1').html().indexOf('-') !== -1).toBe($('mobile-hidden').exists());
    });

    it("Devrait agrandir/réduire les blocs en fonction des dimensions", function() {
      var screenWidth = $('body').width();
      $('body').width(800);
      $(window).trigger('resize');
      expect($('.navbar-inner .input-group.input-group-sm').length).toBe(0);
      expect($('.navbar-inner .btn-group.btn-group-sm').length).toBe(0);
      expect($('.mobile-hidden').length).toBe(0);
      expect(parseInt($('h1').css('font-size'))).toBeGreaterThan(24);
      $('body').width(600);
      $(window).trigger('resize');
      expect($('.navbar-inner .input-group.input-group-sm').length === $('.navbar-inner .input-group').length).toBe(true);
      expect($('.navbar-inner .btn-group.btn-group-sm').length === $('.navbar-inner .btn-group').length).toBe(true);
      expect($('.mobile-hidden').length).toBe(0);
      expect(parseInt($('h1').css('font-size'))).toBeGreaterThan(24);
      $('body').width(200);
      $(window).trigger('resize');
      expect($('.navbar-inner .input-group.input-group-sm').length === $('.navbar-inner .input-group').length).toBe(true);
      expect($('.navbar-inner .btn-group.btn-group-sm').length === $('.navbar-inner .btn-group').length).toBe(true);
      expect($('.mobile-hidden').length === $('.mobile-hidden:hidden').length).toBe(true);
      expect(parseInt($('h1').css('font-size'))).toBeLessThan(24);
      expect($('.navbar-inner .btn-group').css('float')).toBe('right');
      $('body').width(screenWidth);
      $(window).trigger('resize');
    });

  });

});
