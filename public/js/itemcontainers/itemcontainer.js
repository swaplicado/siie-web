/**
 * [whenChangeLink description]
 * @param  {[type]} idSelectLink [description]
 * @return {[type]}                [description]
 */
function whenChangeLink(idSelectLink) {
    // console.log("here");
    var iLinkId = document.getElementById(idSelectLink).value;

    var opt = '<select class="form-control select-one"  name="item_link_id" id="item_link_id">';
    // opt +='<option value="' + 0 + '">Seleccione Tipo...</option>';

    switch (iLinkId) {
      case oData.ALL:
              opt += '<option value="' + 1 + '">' + "TODO" + '</option>';
              break;
      case oData.CLASS:
              oData.lItemClasses.forEach(function(itemClass) {
                  opt += '<option value="' + itemClass.id_item_class + '">' + itemClass.name + '</option>';
              });
              break;
      case oData.TYPE:
              oData.lItemTypes.forEach(function(itemType) {
                  opt += '<option value="' + itemType.id_item_type + '">' + itemType.name + '</option>';
              });
              break;
      case oData.FAMILY:
              oData.lItemFamilies.forEach(function(itemFamily) {
                  opt += '<option value="' + itemFamily.id_item_family + '">' + itemFamily.name + '</option>';
              });
              break;
      case oData.GROUP:
              oData.lItemGroups.forEach(function(itemGroup) {
                  opt += '<option value="' + itemGroup.id_item_group + '">' + itemGroup.name + '</option>';
              });
              break;
      case oData.GENDER:
              oData.lItemGenders.forEach(function(itemGender) {
                  opt += '<option value="' + itemGender.id_item_gender + '">' + itemGender.name + '</option>';
              });
              break;
      case oData.ITEM:
              oData.lItems.forEach(function(item) {
                  opt += '<option value="' + item.id_item + '">' + item.code + '-' + item.name + '</option>';
              });
              break;
      default:

    }

    $('.linid').empty(" ");
    $('.linid').append(opt);

    // $('.wh').chosen({
    //   placeholder_select_single: 'Seleccione un item...'
    // });
}
