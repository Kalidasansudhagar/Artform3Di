<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'status', 'admin_notes'])]
class Enquiry extends Model
{
    /** Status => [label, badge tone]. Tones map to the admin status-badge component. */
    public const STATUSES = [
        'new' => ['New', 'warning'],
        'in_progress' => ['In progress', 'neutral'],
        'quoted' => ['Quoted', 'neutral'],
        'won' => ['Won', 'good'],
        'closed' => ['Closed', 'critical'],
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status][0] ?? ucfirst($this->status);
    }

    public function getStatusToneAttribute(): string
    {
        return self::STATUSES[$this->status][1] ?? 'neutral';
    }
}
