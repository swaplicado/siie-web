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
$('#itm_class').change(function(e) {
		var classid = e.target.value;
		$.get('/genders/' + classid +  '', function(data) {
      console.log(data);
			$('#children').empty();
			$.each(data, function(key, value) {
				var option = $("<option></option>")
	                  .attr("value", key)
	                  .text(value);

				$('#itm_type').append(option);
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
