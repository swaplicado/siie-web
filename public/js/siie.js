/*
* Disable button to avoid double click in forms
*/
function disable(btn) {
        // disable the button
        btn.disabled = true;
        // submit the form
        btn.form.submit();
    }

$(document).ready(function() {
  $('.dropdown-submenu a.test').on("click", function(e){
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });
});

/*
* Comboboxes with dependency
*/
$('#country_id').on('change', function(e) {
    var parent = e.target.value;
    //ajax
    $.get('create/children?parent=' + parent, function(data) {
        //success data
        $('#state_id').empty();
        $.each(data, function(index, subcatObj) {
          var option = $("<option value=" + subcatObj.id_state + "></option>")
  	                  .attr(subcatObj, index)
  	                  .text(subcatObj.name);

  				$('#state_id').append(option);
        });
    });
    $.get('./edit/children?parent=' + parent, function(data) {
        //success data
        $('#state_id').empty();
        $.each(data, function(index, subcatObj) {
          var option = $("<option value=" + subcatObj.id_state + "></option>")
  	                  .attr(subcatObj, index)
  	                  .text(subcatObj.name);

  				$('#state_id').append(option);
        });
    });
});

$('#item_class_id').on('change', function(e) {
    var parent = e.target.value;
    //ajax
    $.get('create/children?parent=' + parent, function(data) {
        //success data
        // console.log(data);
        $('#item_type_id').empty();
        $.each(data, function(index, subcatObj) {
          var option = $("<option value=" + subcatObj.id_item_type + "></option>")
  	                  .attr(subcatObj, index)
  	                  .text(subcatObj.name);

  				$('#item_type_id').append(option);
        });
    });
    $.get('./edit/children?parent=' + parent, function(data) {
        //success data
        $('#item_type_id').empty();
        $.each(data, function(index, subcatObj) {
          var option = $("<option value=" + subcatObj.id_item_type + "></option>")
  	                  .attr(subcatObj, index)
  	                  .text(subcatObj.name);

  				$('#item_type_id').append(option);
        });
    });
});

  /*
  * Change <a> label with keypad
  */
  var li = $('a');
  var liSelected;
  $(window).keydown(function(e) {
      if(e.which === 40) { // down key
          if(liSelected){
              liSelected.removeClass('active');
              next = liSelected.next();
              if(next.length > 0) {
                  liSelected = next.addClass('active');
              }
              else {
                  liSelected = li.eq(0).addClass('active');
              }
          }
          else {
              liSelected = li.eq(0).addClass('active');
          }
      } else if(e.which === 38) { // up key
          if(liSelected) {
              liSelected.removeClass('active');
              next = liSelected.prev();
              if(next.length > 0){
                  liSelected = next.addClass('active');
              }
              else {
                  liSelected = li.last().addClass('active');
              }
          }
          else
          {
              liSelected = li.last().addClass('active');
          }
      }
  });


  /** Clona un objeto (deep-copy)
   * @param  {Any}    from: el objeto a clonar
   * @param  {Object} dest: (opcional) objeto a extender
   * @return {Any} retorna el nuevo objeto clonado
   */
  var fnClone = (function() {
    // @Private
    var _toString = Object.prototype.toString;

    // @Private
    function _clone (from, dest, objectsCache) {
      var prop;
      // determina si @from es un valor primitivo o una funcion
      if (from === null || typeof from !== "object") return from;
      // revisa si @from es un objeto ya guardado en cache
      if (_toString.call(from) === "[object Object]") {
        if (objectsCache.filter(function (item) {
          return item === from;
        }).length) return from;
        // guarda la referencia de los objetos creados
        objectsCache.push(from);
      }
      // determina si @from es una instancia de alguno de los siguientes constructores
      if (from.constructor === Date || from.constructor === RegExp || from.constructor === Function ||
        from.constructor === String || from.constructor === Number || from.constructor === Boolean) {
        return new from.constructor(from);
      }
      if (from.constructor !== Object && from.constructor !== Array) return from;
      // crea un nuevo objeto y recursivamente itera sus propiedades
      dest = dest || new from.constructor();
      for (prop in from) {
        // TODO: allow overwrite existing properties
        dest[prop] = (typeof dest[prop] === "undefined" ?
            _clone(from[prop], null, objectsCache) :
            dest[prop]);
      }
      return dest;
    }

    // función retornada en el closure
    return function (from, dest) {
      var objectsCache = [];
      return _clone(from, dest, objectsCache);
    };

  }());

  function isInt(n){
    return Number(n) === n && n % 1 === 0;
}

function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}

function hasClass(el, className) {
  if (el.classList)
    return el.classList.contains(className)
  else
    return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'))
}

function addClass(el, className) {
  if (el.classList)
    el.classList.add(className)
  else if (!hasClass(el, className)) el.className += " " + className
}

function removeClass(el, className) {
  if (el.classList)
    el.classList.remove(className)
  else if (hasClass(el, className)) {
    var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
    el.className=el.className.replace(reg, ' ')
  }
}
