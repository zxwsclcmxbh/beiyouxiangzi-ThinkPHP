<?php
namespace app\index\model;
use think\Model;

/**
 * @property array|bool|float|int|mixed|object|\stdClass|null user_id
 * @property array|bool|float|int|mixed|object|\stdClass|null icon
 * @property array|bool|float|int|mixed|object|\stdClass|null name
 */
class User extends Model
{
    protected $table = 'user';
}