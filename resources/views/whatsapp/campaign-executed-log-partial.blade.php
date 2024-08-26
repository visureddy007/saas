   
{{-- datatable executed log --}}
<x-lw.datatable lw-card-classes="border-0" data-page-length="100" id="lwCampaignQueueLog" :url="route('vendor.campaign.executed.log.list.view', ['campaignUid' => $campaignUid])">
    <th data-orderable="true" data-name="full_name">{{ __tr('Name') }}</th>
    <th data-orderable="true" data-name="contact_wa_id">{{ __tr('Phone Number') }}</th>
    <th data-orderable="true" data-template="#campaignStatusMessage" data-name="messaged_at">{{ __tr('Message Delivery Status') }}</th>
    <th data-orderable="true" data-order-by="true" data-order-type="desc" data-name="updated_at">{{ __tr('Last Status Updated at') }}</th>
</x-lw.datatable>
{{-- /datatable executed log --}}
<script type="text/template" id="campaignStatusMessage">
    <% if (__tData.status == 'failed') { %>
        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{  __tr('Failed') }} - 
        <small class="text-danger"><%- __tData.whatsapp_message_error %></small></span>
    <% } else if(__tData.status == 'sent') { %>
        <span><svg width="16" height="16"
            viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M4 12.6111L8.92308 17.5L20 6.5"
                stroke="#92a58c"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round" />
        </svg> {{  __tr('Sent') }}</span>
    <% } else if(__tData.status == 'delivered') { %>
        <span><svg xmlns="http://www.w3.org/2000/svg"
            width="16" height="15"
            id="msg-dblcheck" x="2047"
            y="2061">
            <path
                d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"
                fill="#92a58c" />
        </svg> {{  __tr('Delivered') }}</span>
    <% } else if(__tData.status == 'read') { %>
        <span><svg xmlns="http://www.w3.org/2000/svg"
            width="16" height="15"
            id="msg-dblcheck-ack" x="2063"
            y="2076">
            <path
                d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"
                fill="#4fc3f7" />
        </svg> {{  __tr('Read') }}</span>
    <% } else { %>
        <%- __tData.status %>
    <% } %>
</script>
