<?php

namespace App\Enums;

enum UserPermission: string
{
    case ADMIN_PERMISSIONS  = "admin-permissions";
    case ACCEPT_ORDER  = "accept-order";
    case REJECT_ORDER  = "reject-order";
    case START_ORDER  = "start-order";
    case END_ORDER  = "end-order";
    case CREATE_CART_ITEM  = "create-cart-item";
    case UPDATE_CART_ITEM  = "update-cart-item";
    case DELETE_CART_ITEM  = "delete-cart-item";
    case CONFIRM_ORDER  = "confirm-order";
    case DELETE_ORDER  = "delete-order";
}
