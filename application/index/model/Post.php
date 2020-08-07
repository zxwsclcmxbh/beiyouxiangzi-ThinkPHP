<?php
namespace app\index\model;
use think\Model;

/**
 * @property array|bool|float|int|mixed|object|\stdClass|null post_id
 * @property array|bool|float|int|mixed|object|\stdClass|null tag
 * @property array|bool|float|int|mixed|object|\stdClass|null time
 * @property array|bool|float|int|mixed|object|\stdClass|null body
 * @property array|bool|float|int|mixed|object|\stdClass|null pic_url
 * @property array|bool|float|int|mixed|object|\stdClass|null likes
 * @property array|bool|float|int|mixed|object|\stdClass|null layer
 * @property array|bool|float|int|mixed|object|\stdClass|null user_id
 */
class Post extends Model
{
    protected $table = 'post';
}