<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

Schema::create('templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('department_id')->constrained();
    $table->string('background_front')->nullable();
    $table->string('background_back')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->json('editable_fields');
    $table->text('back_content')->nullable();
    $table->float('logo_x')->nullable();
    $table->float('logo_y')->nullable();
    $table->float('logo_width')->nullable();
    $table->float('text_start_x')->nullable();
    $table->float('text_start_y')->nullable();
    $table->timestamps();
});

class Template extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'file_path', 'description', 'is_double_sided', 'editable_fields', 'is_active'];
    
    protected $casts = [
        'editable_fields' => 'array',
        'is_double_sided' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
