<?php

namespace models;

enum Role: string{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
}