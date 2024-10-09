<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN  = "admin";
    case SALES_MANAGER  = "sales manager";
    case DELIVERY  = "delivery";
    case CUSTOMER  = "customer";
}