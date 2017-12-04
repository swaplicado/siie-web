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
