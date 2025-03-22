<?php

namespace Srapid\Location\Http\Requests;

use Srapid\Support\Http\Requests\Request;

class BulkImportRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $mimeType = implode(',', config('plugins.location.general.bulk-import.mime_types', []));

        return [
            'file' => 'required|file|mimetypes:' . $mimeType,
            'type' => 'required|in:all,countries,states,cities',
        ];
    }
}
