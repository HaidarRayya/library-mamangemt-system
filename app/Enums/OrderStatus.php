<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PINDING  = "pinding";
    case ACCEPTED = "accepted";
    case REJECTED  = "rejected";
    case START_DELIVERY  = "start-delivery";
    case DELIVERED   = "delivered";
}
