<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait SoftDeletesWithUser
{
    use SoftDeletes;

    protected static function bootSoftDeletesWithUser()
    {
        static::deleting(function (Model $model) {
            if ($model->isForceDeleting()) {
                return;
            }
            $model->deleted_by = Auth::id();
            $model->save();
        });

        static::restoring(function (Model $model) {
            $model->deleted_by = null;
            $model->save();
        });
    }
}
