<?php

namespace App\Http\Controllers\BlastingCampaignRecipient;

use App\Http\Controllers\Controller;
use App\Models\BlastingCampaign;
use App\Models\BlastingRecipient;
use App\Services\BlastingCampaignRecipientService;
use Illuminate\Http\Request;

class BlastingCampaignRecipientController extends Controller
{
    protected BlastingCampaignRecipientService $service;

    public function __construct(BlastingCampaignRecipientService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX - List recipients in campaign
    |--------------------------------------------------------------------------
    */
    public function index(BlastingCampaign $campaign)
    {
        $recipients = $this->service->paginate($campaign, 20);

        return response()->json($recipients);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE - Attach recipients to campaign
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, BlastingCampaign $campaign)
    {
        $validated = $request->validate([
            'recipient_ids'   => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['exists:blasting_recipients,id'],
        ]);

        $this->service->attachRecipients(
            $campaign,
            $validated['recipient_ids']
        );

        return response()->json([
            'message' => 'Recipients added to campaign',
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY - Remove recipient from campaign
    |--------------------------------------------------------------------------
    */
    public function destroy(
        BlastingCampaign $campaign,
        BlastingRecipient $recipient
    ) {
        try {
            $this->service->detachRecipient($campaign, $recipient);

            return response()->json([
                'message' => 'Recipient removed from campaign',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RETRY - Retry failed recipient
    |--------------------------------------------------------------------------
    */
    public function retry(
        BlastingCampaign $campaign,
        BlastingRecipient $recipient
    ) {
        try {
            $this->service->retryRecipient($campaign, $recipient);

            return response()->json([
                'message' => 'Recipient queued for retry',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
