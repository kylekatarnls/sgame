// Generated by CoffeeScript PHP 1.3.1

$(document).on('click', 'a.toggle-next', function() {
  var $a, $next;
  $a = $(this);
  $next = $a.nextAll('.toggleable');
  if (!$next.length) {
    $next = $a.parent().nextAll('.toggleable');
  }
  $next.slideToggle();
  return false;
}).on('click', 'p.image, p.no-image, .retina, .no-retina', function() {
  var $p, $prev;
  $p = $(this);
  $prev = $p.prevAll('.upload');
  if (!$prev.length) {
    $prev = $a.parent().prevAll('.toggleable');
  }
  $prev.find('input[type="file"]').click();
  return false;
}).on('change', '.upload input[type="file"]', function() {
  return this.form.submit();
}).on('change', '.upload input[type="checkbox"]', function() {
  var data;
  data = {};
  data[$(this).attr('name')] = $(this).prop('checked') ? '1' : '0';
  return ajax('survey/image/to-be-replaced', data, function(res) {});
}).on('click', '.git-diff', function() {
  var $diff, $link;
  $link = $(this);
  $diff = $link.next('.diff');
  if ($diff.hasClass('open')) {
    $diff.removeClass('open').html('');
  } else {
    $diff.addClass('open').html('\nLoading...');
    ajax('survey/diff', {
      file: $link.attr('href')
    }, function(res) {
      var html;
      console.log(res);
      if (res.diff) {
        html = '';
        $.each(res.diff.split(/\n/g), function() {
          var classAttr;
          classAttr = (function() {
            switch (this.charAt(0)) {
              case '@':
                return ' class="comment-line"';
              case '+':
                return ' class="add-line"';
              case '-':
                return ' class="remove-line"';
              default:
                return '';
            }
          }).call(this);
          return html += '<div' + classAttr + '>' + this + '</div>';
        });
        return $diff.html(html);
      }
    });
  }
  return false;
});

$(function() {
  return $('p.image').hover(function() {
    var $img, h, text, timeout, w;
    $img = $(this).find('img');
    w = $img.width();
    h = $img.height();
    timeout = $img.data('timeout-detail');
    if (timeout) {
      return clearTimeout(timeout);
    } else {
      $img.next('span.detail').remove();
      text = w + ' x ' + h + ' &nbsp; Retina : ' + (w * 2) + ' x ' + (h * 2);
      return $('<span class="detail">' + text + '</span>').fadeOut(0).fadeIn().insertAfter($img);
    }
  }, function() {
    var $img;
    $img = $(this).find('img');
    return $img.data('timeout-detail', setTimeout(function() {
      return $img.removeData('timeout-detail').next('span.detail').fadeOut(function() {
        return $(this).remove();
      });
    }, 400));
  });
});
