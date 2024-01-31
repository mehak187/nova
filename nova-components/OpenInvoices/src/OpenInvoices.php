<?php

namespace FirstpointCh\OpenInvoices;

use App\Models\Client;
use Laravel\Nova\ResourceTool;

class OpenInvoices extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Open Invoices';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'open-invoices';
    }
}
