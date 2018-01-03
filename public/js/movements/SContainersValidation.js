function validateContainer(oWarehouse, oItem, lItemContainers) {
  var isValid = false;

    lItemContainers.forEach(function(itemContainer) {
        if (itemContainer.container_type_id == globalData.CONTAINER_NA ||
              itemContainer.container_type_id == globalData.CONTAINER_COMPANY ||
              (itemContainer.container_type_id == globalData.CONTAINER_BRANCH && itemContainer.container_id == oWarehouse.branch_id) ||
              (itemContainer.container_type_id == globalData.CONTAINER_WAREHOUSE && itemContainer.container_id == oWarehouse.id_whs)
        ) {

          switch ('' + itemContainer.item_link_type_id) {
            case globalData.LINK_ALL:
              isValid = true;
              break;

            case globalData.LINK_CLASS:
              isValid = itemContainer.item_link_id == oItem.gender.item_class_id;
              break;

            case globalData.LINK_TYPE:
              isValid = itemContainer.item_link_id == oItem.gender.item_type_id;
              break;

            case globalData.LINK_FAMILY:
              isValid = itemContainer.item_link_id == oItem.gender.group.item_family_id;
              break;

            case globalData.LINK_GROUP:
              isValid = itemContainer.item_link_id == oItem.gender.group.id_item_group;
              break;

            case globalData.LINK_GENDER:
              isValid = itemContainer.item_link_id == oItem.item_gender_id;
              break;

            case globalData.LINK_ITEM:
              isValid = itemContainer.item_link_id == oItem.id_item;
              break;

            default:

          }

          if (isValid) {
            return true;
          }
        }
    });

    return isValid;

}


function validateItemWarehouseType(oWarehouse, item) {
  isValid = false;
  var CLASS_MATERIAL = 1;
  var CLASS_PRODUCT = 2;
  var CLASS_SPENDING = 3;

  var  WHS_TYPE_NA = 1;
  var  WHS_TYPE_MATERIAL = 2;
  var  WHS_TYPE_PRODUCTION = 3;
  var  WHS_TYPE_PRODUCT = 4;

  /**
   *  Item classes
   *  1	MATERIAL
   *  2	PRODUCT
   *  3	SPENDING
   */
  /**
   *  Whs types
   *  1 N/A
   *  2 MATERIAL
   *  3 PRODUCTION
   *  4 PRODUCT
   */


    switch (oWarehouse.whs_type.id_whs_type) {
      case WHS_TYPE_NA:
              isValid = true;
              return true;
        break;
      case WHS_TYPE_MATERIAL:
              if (item.gender.item_class.id_item_class == CLASS_MATERIAL) {
                isValid = true;
                return true;
              }
        break;
      case WHS_TYPE_PRODUCTION:
              if (item.gender.item_class.id_item_class == CLASS_PRODUCT || item.gender.item_class.id_item_class == CLASS_MATERIAL) {
                isValid = true;
                return true;
              }
        break;
      case WHS_TYPE_PRODUCT:
              if (item.gender.item_class.id_item_class == CLASS_PRODUCT) {
                isValid = true;
                return true;
              }
        break;
      default:

    }


  if (!isValid) {
      // alert("No puede ingresar este material/producto en este almacén");
      swal("Error", "No puede ingresar este material/producto en este almacén.", "error");
  }

  return isValid;
}
