/*
* Disable button to avoid double click in forms
*/
function disable(btn) {
        // disable the button
        btn.disabled = true;
        // submit the form
        btn.form.submit();
    }

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
