<?php
 
namespace App\models;
use App\User;
use Illuminate\Database\Eloquent\Model;
 
class Post extends Model
{
    protected $fillable = [
        'id', 'user_id', 'type', 'latitude', 'longitude', 'time_of_post', 'image_ids_array', 'ip_address', 'likes', 'comments', 'shares', 'venue_of_event', 'time_of_event', 'price_of_buy_sell', 'location_of_buy_sell'
    ];
}