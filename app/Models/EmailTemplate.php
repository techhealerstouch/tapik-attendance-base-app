<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
        'available_variables',
        'is_active'
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function render(array $data = [])
    {
        $content = $this->content;
        
        foreach ($data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }
        
        return $content;
    }

    public function renderSubject(array $data = [])
    {
        $subject = $this->subject;
        
        foreach ($data as $key => $value) {
            $subject = str_replace('{{ ' . $key . ' }}', $value, $subject);
        }
        
        return $subject;
    }
}