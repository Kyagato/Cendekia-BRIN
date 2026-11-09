<?php

namespace App\Http\Requests;

class UpdateKnowledgeRequest extends KnowledgeRequest
{
    protected function isUpdate(): bool
    {
        return true;
    }
}
