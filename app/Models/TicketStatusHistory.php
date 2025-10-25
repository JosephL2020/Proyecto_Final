<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TicketStatusHistory extends Model{
protected $fillable=['ticket_id','from_status','to_status','changed_by'];
public function ticket(){return $this->belongsTo(Ticket::class);}
public function changer(){return $this->belongsTo(User::class, 'changed_by');}
}