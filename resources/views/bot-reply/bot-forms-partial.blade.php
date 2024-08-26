<!-- Add New Advance Bot Reply Modal -->
<x-lw.modal id="lwAddNewAdvanceBotReply" modal-dialog-class="modal-lg" :header="__tr('Add New Bot Reply')" :hasForm="true">
    <!--  Add New Bot Reply Form -->
    <x-lw.form x-data="{triggerType:'',headerType:'',interactiveButtonType:'button'}" id="lwAddNewAdvanceBotReplyForm"
        :action="route('vendor.bot_reply.write.create')"
        :data-callback-params="['modalId' => '#lwAddNewAdvanceBotReply', 'datatableId' => '#lwBotReplyList']"
        data-callback="appFuncs.modelSuccessCallback">
        <!-- form body -->
        <div class="lw-form-modal-body">
            <!-- form fields form fields -->
            <input type="hidden" name="message_type" :value="isAdvanceBot">
            <template x-if="botFlowUid">
                <input type="hidden" name="bot_flow_uid" :value="botFlowUid">
            </template>
            <!-- Name -->
            <x-lw.input-field type="text" id="lwAdvanceBotNameField" data-form-group-class="" :label="__tr('Name')"
                name="name" required="true" />
            <!-- /Name -->
            <fieldset>
                <legend>{{  __tr('Reply Message') }}</legend>
                <!-- Reply_Text -->
                <div class="form-group" x-show="isAdvanceBot == 'simple' || isAdvanceBot == 'interactive'">
                    <label for="lwReplyTextField">{{ __tr('Reply Text') }}</label>
                    <textarea cols="10" rows="3" id="lwAdvanceBotReplyTextField" class="lw-form-field form-control"
                        placeholder="{{ __tr('Add your main message body text here') }}" name="reply_text" required="true"></textarea>
                        <div class="help-text my-3 border p-3">{{  __tr('You are free to use following dynamic variables for reply text, which will get replaced with contact\'s concerned field value.') }} <div><code>{{ implode(' ', $dynamicFields) }}</code></div></div>
                </div>
                <!-- /Reply_Text -->
                <div x-show="isAdvanceBot == 'interactive' || isAdvanceBot == 'media'">
                        {{-- select type --}}
                    <div x-show="isAdvanceBot == 'interactive'">
                    <x-lw.input-field x-init="$watch('headerType', function() {
                        if((interactiveButtonType == 'list') && !_.includes(['','text'],headerType)) {
                            interactiveButtonType = 'button';
                        }
                    })" x-model="headerType" type="selectize" id="lwAdvanceBotHeaderTypeField"  data-form-group-class="" data-selected=" " :label="__tr('Header Type (optional)')" name="header_type" >
                        <x-slot name="selectOptions">
                            <option value="">{{  __tr('None') }}</option>
                            <option value="text">{{  __tr('Text') }}</option>
                            <option value="image">{{  __tr('Image') }}</option>
                            <option value="video">{{  __tr('Video') }}</option>
                            <option value="document">{{  __tr('Document') }}</option>
                        </x-slot>
                    </x-lw.input-field>
                    </div>
                    <div x-show="isAdvanceBot == 'media'">
                        <x-lw.input-field x-model="headerType" type="selectize" id="lwMediaHeaderType"
                        data-form-group-class="" data-selected=" " :label="__tr('Header Type')" name="media_header_type" >
                            <x-slot name="selectOptions">
                                <option value="">{{  __tr('None') }}</option>
                                <option value="image">{{  __tr('Image') }}</option>
                                <option value="video">{{  __tr('Video') }}</option>
                                <option value="document">{{  __tr('Document') }}</option>
                                <option value="audio">{{  __tr('Audio') }}</option>
                            </x-slot>
                        </x-lw.input-field>
                    </div>
                    <div class="my-3">
                    {{-- document --}}
                    <div x-show="headerType == 'document'" class="form-group col-sm-12">
                    <input id="lwDocumentMediaFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Select Document') }}" class="lw-file-uploader" data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'whatsapp_document') ?>" id="lwDocumentField" data-file-input-element="#lwMediaFileName" data-allowed-media='<?= getMediaRestriction('whatsapp_document') ?>' />
                    </div>
                    {{-- image --}}
                    <div x-show="headerType == 'image'" class="form-group col-sm-12">
                    <input id="lwImageMediaFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Select Image') }}" class="lw-file-uploader" data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'whatsapp_image') ?>" id="lwImageField" data-file-input-element="#lwMediaFileName" data-allowed-media='<?= getMediaRestriction('whatsapp_image') ?>' />
                </div>
                {{-- video --}}
                <div x-show="headerType == 'video'" class="form-group col-sm-12">
                    <input id="lwVideoMediaFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Select Video') }}" class="lw-file-uploader" data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'whatsapp_video') ?>" id="lwVideoField" data-file-input-element="#lwMediaFileName" data-allowed-media='<?= getMediaRestriction('whatsapp_video') ?>' />
                    </div>
                {{-- audio --}}
                    <div x-show="headerType == 'audio'" class="form-group col-sm-12">
                    <input id="lwAudioMediaFilepond" type="file" data-allow-revert="true"
                        data-label-idle="{{ __tr('Select Audio') }}" class="lw-file-uploader" data-instant-upload="true"
                        data-action="<?= route('media.upload_temp_media', 'whatsapp_audio') ?>" id="lwAudioField" data-file-input-element="#lwMediaFileName" data-allowed-media='<?= getMediaRestriction('whatsapp_audio') ?>' />
                    </div>
                </div>
                        <input id="lwMediaFileName" type="hidden" value="" name="uploaded_media_file_name" />
                        <div x-show="(isAdvanceBot == 'media') && headerType && (headerType != 'audio')">
                        <label for="lwMediaCaptionText">{{  __tr('Caption/Text') }}</label>
                        <textarea name="caption" id="lwCaptionField" class="form-control" rows="2"></textarea>
                        <div class="help-text my-3 border p-3">{{  __tr('You are free to use following dynamic variables for caption, which will get replaced with contact\'s concerned field value.') }} <div><code>{{ implode(' ', $dynamicFields) }}</code></div></div>
                    </div>
                        <div x-show="headerType == 'text'">
                        <x-lw.input-field type="text" id="lwAdvanceHeaderText" data-form-group-class=""
                            :label="__tr('Header Text')" name="header_text" required="true" />
                </div>
                    <div class="mt-4" x-show="isAdvanceBot == 'interactive'">
                       <strong> <input type="radio" name="interactive_type" x-model="interactiveButtonType" value="button" id="lwNewAdvanceBotReplyBtnType"> <label  class="mr-2" for="lwNewAdvanceBotReplyBtnType">{{  __tr('Reply Buttons') }}</label>
                        <input type="radio" name="interactive_type" x-model="interactiveButtonType" value="cta_url" id="lwNewAdvanceBotCtaUrlBtnType"> <label class="mr-2" for="lwNewAdvanceBotCtaUrlBtnType">{{  __tr('CTA URL Button') }}</label>
                        <input type="radio" x-bind:disabled="!_.includes(['','text'],headerType)" name="interactive_type" x-model="interactiveButtonType" value="list" id="lwNewAdvanceBotListMessageType"> <label x-bind:class="!_.includes(['','text'],headerType) ? 'text-muted' : ''" class="mr-2" for="lwNewAdvanceBotListMessageType">{{  __tr('List Message') }} <abbr x-show="!_.includes(['','text'],headerType)" title="{{  __tr('Header is optional, Only text type for header is supported for the list message') }}">?</abbr></label></strong>
                        <hr class="mt-1 mb-2">
                    <template x-if="interactiveButtonType == 'button'">
                        <div>
                            {{-- <h2>{{  __tr('Reply Buttons') }}</h2> --}}
                            <x-lw.input-field type="text" id="lwAdvanceButton1" data-form-group-class="" :label="__tr('Button 1 Label')" name="buttons[1]" required="true" />
                            <x-lw.input-field type="text" id="lwAdvanceButton2" data-form-group-class="" :label="__tr('Button 2 Label (optional)')" name="buttons[2]" />
                            <x-lw.input-field type="text" id="lwAdvanceButton3" data-form-group-class="" :label="__tr('Button 3 Label (optional)')" name="buttons[3]" />
                        </div>
                    </template>
                    <template x-if="interactiveButtonType == 'cta_url'">
                        <div>
                            {{-- <h2>{{  __tr('CTA URL Button') }}</h2> --}}
                            <x-lw.input-field type="text" id="lwCtaUrlButtonDisplayText" data-form-group-class="" :label="__tr('CTA Button Display Text')" name="button_display_text" required="true"/>
                            <x-lw.input-field type="text" id="lwCtaButtonUrl" data-form-group-class="" :label="__tr('CTA Button URL')" name="button_url" required="true" />
                        </div>
                    </template>
                    <template x-if="interactiveButtonType == 'list'">
                        <div x-data="{botListMessageSections:{}, addListSection : function() {
                            let uniqueSectionId = __Utils.generateUniqueId('section_');
                            this.botListMessageSections[uniqueSectionId] = {
                                index: _.size(this.botListMessageSections) + 1,
                                id:uniqueSectionId,
                                title:'',
                                rows:{}
                            };
                          },addListSectionRow : function(sectionId) {
                            let uniqueRowId = __Utils.generateUniqueId('row_');
                            this.botListMessageSections[sectionId]['rows'][uniqueRowId] = {
                                index: _.size(this.botListMessageSections[sectionId]['rows']) + 1,
                                id: uniqueRowId,
                                row_id:'',title:'',
                                description:''
                            };
                            },deleteSection(sectionId){
                            delete this.botListMessageSections[sectionId];
                            },deleteRow(sectionId, rowId){
                            delete this.botListMessageSections[sectionId]['rows'][rowId];
                           }}" >
                            {{-- <h2>{{  __tr('List Message') }}</h2> --}}
                            <x-lw.input-field type="text" id="lwListButtonText" data-form-group-class="" :label="__tr('Button Label')" name="list_button_text" required="true" />
                            <template x-for="(botListMessageSection, index) in botListMessageSections">
                                <fieldset>
                                    <legend  class="py-1 px-2 mb-1"><small>{{  __tr('Section') }}</small></legend>
                                    <button @click.prevent="deleteSection(botListMessageSection.id)" class="btn btn-link float-right p-1 mt--4" type="button"><i class="fa fa-times text-danger"></i></button>
                                    <x-lw.input-field type="text" id="lwListMessageSectionTitle" data-form-group-class="" :label="__tr('Section Title')" x-bind:name="'sections[' + botListMessageSection.id+'][title]'" required="true" />
                                    <input type="hidden" x-bind:name="'sections[' + botListMessageSection.id+'][id]'" x-bind:value="botListMessageSection.id" required="true">
                                    <template x-for="botListMessageRow in botListMessageSection.rows">
                                    <fieldset>
                                        <legend  class="py-1 px-2 mb-1"><small>{{  __tr('Row') }}</small></legend>
                                        {{-- delete row btn --}}
                                        <button @click.prevent="deleteRow(botListMessageSection.id, botListMessageRow.id)" class="btn btn-link float-right p-1 mt--4" type="button"><i class="fa fa-times text-danger"></i></button>
                                        <input type="hidden" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][id]'" x-bind:value="botListMessageRow.id" required="true">
                                        {{-- row id --}}
                                        <x-lw.input-field type="text" x-bind:id="'lwListMessageRowId'+botListMessageSection.id+botListMessageRow.id" data-form-group-class="" :label="__tr('Row ID')" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][row_id]'" required="true" />
                                        {{-- row title --}}
                                        <x-lw.input-field type="text" x-bind:id="'lwListMessageRowTitle'+botListMessageSection.id+botListMessageRow.id" data-form-group-class="" :label="__tr('Row Title')" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][title]'" required="true" />
                                        {{-- row description --}}
                                        <div class="form-group">
                                            <label for="">{{ __tr('Row Description (optional)') }}</label>
                                            <textarea class="form-control" rows="2" x-bind:id="'lwListMessageRowDescription'+botListMessageSection.id+botListMessageRow.id" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][description]'"></textarea>
                                        </div>
                                    </fieldset>
                                </template>
                                <button type="button" class="btn btn-sm btn-light my-2" @click="addListSectionRow(botListMessageSection.id)">{{  __tr('Add Row') }}</button>
                                </fieldset>
                            </template>
                            <button type="button" class="btn btn-sm btn-dark my-2" @click="addListSection()">{{  __tr('Add Section') }}</button>
                        </div>
                        </template>
                    </div>
                    {{-- footer text --}}
                    <div x-show="isAdvanceBot == 'interactive'">
                        <x-lw.input-field  type="text" id="lwAdvanceFooterText" data-form-group-class=""
                        :label="__tr('Footer Text (optional)')" name="footer_text" />
                    </div>
                </div>
                {{-- /reply --}}
            </fieldset>
            <template x-if="!botFlowUid">
                <div>
                    <!-- Trigger_Type -->
                <x-lw.input-field x-model="triggerType" type="selectize" id="lwAdvanceBotTriggerTypeField"
                data-form-group-class="" data-selected=" " :label="__tr('Trigger Type')" name="trigger_type"
                required="true">
                <x-slot name="selectOptions">
                    <option value="">{{ __tr('How do you want to trigger this message?') }}</option>
                    @foreach (configItem('bot_reply_trigger_types') as $replyBotTypeKey => $replyBotType)
                    <option value="{{ $replyBotTypeKey }}">{{ $replyBotType['title'] }} </option>
                    @endforeach
                </x-slot>
            </x-lw.input-field>
            <!-- /Trigger_Type -->
            @foreach (configItem('bot_reply_trigger_types') as $replyBotTypeKey => $replyBotType)
            <div x-show="triggerType == '{{ $replyBotTypeKey }}'" class="alert alert-dark">{{
                $replyBotType['description'] }}</div>
            @endforeach
            <!-- Reply_Trigger -->
            <div x-show="triggerType != 'welcome'">
                <x-lw.input-field placeholder="{{ __tr('What\'s that magic words that will trigger this reply?') }}" type="text" id="lwAdvanceBotReplyTriggerField" data-form-group-class=""
                    :label="__tr('Reply Trigger Subject')" name="reply_trigger" required="true" />
                    <div><small class="text-muted">{{  __tr('You can have comma separated multiple triggers.') }}</small></div>
            </div>
            <!-- /Reply_Trigger -->
            <div class="form-group pt-3">
                <x-lw.checkbox id="lwAddBotStatus" :offValue="0" value="1" :checked="true" name="status" data-lw-plugin="lwSwitchery" :label="__tr('Status')" />
             </div>
                </div>
            </template>
            <template x-if="botFlowUid">
                <input type="hidden" name="trigger_type" value="is">
            </template>
            <div class="my-4">
                <x-lw.checkbox id="lwValidateBotReply" :offValue="0" name="validate_bot_reply" value="1" data-lw-plugin="lwSwitchery" :label="__tr('Validate Bot Reply by Sending Test Message')" />
            </div>
        </div>
        <!-- form footer -->
        <div class="modal-footer">
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
        </div>
    </x-lw.form>
    <!--/  Add New Bot Reply Form -->
</x-lw.modal>
<!--/ Add New Advance Bot Reply Modal -->
<!-- Edit Bot Reply Modal -->
<x-lw.modal id="lwEditBotReply" modal-dialog-class="modal-lg" :header="__tr('Edit Bot Reply')" :hasForm="true">
    <!--  Edit Bot Reply Form -->
    <x-lw.form id="lwEditBotReplyForm" :action="route('vendor.bot_reply.write.update')"
        :data-callback-params="['modalId' => '#lwEditBotReply', 'datatableId' => '#lwBotReplyList']"
        data-callback="appFuncs.modelSuccessCallback" x-data="{headerType:''}">
        <!-- form body -->
        <div id="lwEditBotReplyBody" class="lw-form-modal-body"></div>
        <script type="text/template" id="lwEditBotReplyBody-template">
            <div>
            <input type="hidden" name="botReplyIdOrUid" value="<%- __tData._uid %>" />
            <template x-if="botFlowUid">
                <input type="hidden" name="bot_flow_uid" :value="botFlowUid">
            </template>
                <!-- form fields -->
                <!-- Name -->
            <x-lw.input-field type="text" id="lwNameEditField" data-form-group-class="" :label="__tr('Name')" value="<%- __tData.name %>" name="name"  required="true"                 />
        <!-- /Name -->
        <fieldset>
            <legend>{{  __tr('Reply') }}</legend>
        <!-- Reply_Text -->
        <% if(!__tData.__data?.media_message)  { %>
        <div class="form-group">
        <label for="lwReplyTextEditField">{{ __tr('Reply Text') }}</label>
        <textarea cols="10" rows="3" id="lwReplyTextEditField" value="<%- __tData.reply_text %>" class="lw-form-field form-control" placeholder="{{ __tr('Reply Text') }}" name="reply_text"  required="true"><%- __tData.reply_text %></textarea>
        <div class="help-text my-3 border p-3">{{  __tr('You are free to use following dynamic variables for reply text, which will get replaced with contact\'s concerned field value.') }} <div><code>{{ implode(' ', $dynamicFields) }}</code></div></div>
            </div>
            <% } %>
            <% if(__tData.__data?.media_message)  { %>
            <input type="hidden" name="message_type" value="media">
            <input type="hidden" name="media_header_type" value="<%- __tData.__data?.media_message.header_type %>">
            <fieldset>
                <div class="text-center">
                    <h2 class="text-center"> <%- __tData.__data?.media_message.header_type %></h2>
                    <div class="lw-whatsapp-header-placeholder py-3">
                        <% if(__tData.__data?.media_message.header_type == 'video')  { %>
                            <video class="lw-whatsapp-header-video" controls src="<%- __tData.__data?.media_message.media_link %>"></video>
                        <% } else if(__tData.__data?.media_message.header_type == 'image') { %>
                            <img class="lw-whatsapp-header-image" src="<%- __tData.__data?.media_message.media_link %>" alt="">
                        <% } else if(__tData.__data?.media_message.header_type == 'audio') { %>
                            <audio class="lw-whatsapp-header-audio my-auto mx-4" controls>
                                <source src="<%- __tData.__data?.media_message.media_link %>">
                            {{  __tr('Your browser does not support the audio element.') }}
                            </audio>
                        <% } else if(__tData.__data?.media_message.header_type && (__tData.__data?.media_message.header_type != 'text')) { %>
                            <a target="blank" class="btn btn-dark" href="<%- __tData.__data?.media_message.media_link %>">{{  __tr('Media Link') }}</a>
                        <% } %>
                    </div>
                </div>
                <% if(__tData.__data?.media_message.header_type != 'audio') { %>
                <div class="form-group">
                    <label for="lwMediaCaptionText">{{  __tr('Caption/Text') }}</label>
                    <textarea name="caption" id="lwCaptionField" class="form-control" rows="2"><%- __tData.__data?.media_message.caption %></textarea>
                    <div class="help-text my-3 border p-3">{{  __tr('You are free to use following dynamic variables for caption, which will get replaced with contact\'s concerned field value.') }} <div><code>{{ implode(' ', $dynamicFields) }}</code></div></div>
                </div>
                <% } %>
            </fieldset>
            <% } else if(__tData.__data?.interaction_message)  { %>
                <input type="hidden" name="message_type" value="interactive">
                {{-- <input type="hidden" name="interactive_type" value="<%- (__tData.__data?.interaction_message.interactive_type && __tData.__data?.interaction_message.interactive_type == 'cta_url') ? 'cta_url' : 'button' %>"> --}}
                <input type="hidden" name="interactive_type" value="<%- (__tData.__data?.interaction_message.interactive_type) ? __tData.__data?.interaction_message.interactive_type : 'button' %>">
                <input type="hidden" name="header_type" value="<%- __tData.__data?.interaction_message.header_type %>">
                <fieldset>
                    <div class="text-center">
                        <h2 class="text-center"> <%- __tData.__data?.interaction_message.header_type %></h2>
                        <%if(__tData.__data?.interaction_message.header_type && (__tData.__data?.interaction_message.header_type != 'text')) { %>
                        <div class="lw-whatsapp-header-placeholder py-3">
                            <% if(__tData.__data?.interaction_message.header_type == 'video')  { %>
                                <video class="lw-whatsapp-header-video" controls src="<%- __tData.__data?.interaction_message.media_link %>"></video>
                            <% } else if(__tData.__data?.interaction_message.header_type == 'image') { %>
                                <img class="lw-whatsapp-header-image" src="<%- __tData.__data?.interaction_message.media_link %>" alt="">
                            <% } else { %>
                                <a target="blank" class="btn btn-dark" href="<%- __tData.__data?.interaction_message.media_link %>">{{  __tr('Media Link') }}</a>
                            <% } %>
                        </div>
                        <% } %>
                    </div>
                    <div class="my-3">
                    {{-- document --}}
                    <% if(__tData.__data?.interaction_message.header_type == 'text')  { %>
                    <x-lw.input-field type="text" id="lwAdvanceEditHeaderText" data-form-group-class=""
                        :label="__tr('Header Text')" value="<%- __tData.__data?.interaction_message.header_text %>" name="header_text" required="true" />
                        <% } %>

                        <% if(__tData.__data?.interaction_message.interactive_type && __tData.__data?.interaction_message.interactive_type == 'list') { window.tempSectionData = _.get(__tData, '__data.interaction_message.list_data.sections', {}); %>
                            <div x-data="{botListMessageSections:window.tempSectionData, addListSection : function() {
                                let uniqueSectionId = __Utils.generateUniqueId('section_');
                                this.botListMessageSections[uniqueSectionId] = {
                                    index: _.size(this.botListMessageSections) + 1,
                                    id:uniqueSectionId,
                                    title:'',
                                    rows:{}
                                };
                            },addListSectionRow : function(sectionId) {
                                let uniqueRowId = __Utils.generateUniqueId('row_');
                                this.botListMessageSections[sectionId]['rows'][uniqueRowId] = {
                                    index: _.size(this.botListMessageSections[sectionId]['rows']) + 1,
                                    id: uniqueRowId,
                                    row_id:'',title:'',
                                    description:''
                                };
                            },deleteSection(sectionId){
                                delete this.botListMessageSections[sectionId];
                            },deleteRow(sectionId, rowId){
                                delete this.botListMessageSections[sectionId]['rows'][rowId];
                            }}" >
                                <h2>{{  __tr('List Message') }}</h2>
                                <x-lw.input-field type="text" value="<%- __tData.__data?.interaction_message.list_data.button_text %>" id="lwListButtonText" data-form-group-class="" :label="__tr('Button Label')" name="list_button_text" required="true" />
                                <template x-for="botListMessageSection in botListMessageSections">
                                    <fieldset>
                                        <legend  class="py-1 px-2 mb-1"><small>{{  __tr('Section') }}</small></legend>
                                        <input type="hidden" x-bind:name="'sections[' + botListMessageSection.id+'][id]'" x-bind:value="botListMessageSection.id" required="true">
                                        <button @click.prevent="deleteSection(botListMessageSection.id)" class="btn btn-link float-right p-1 mt--4" type="button"><i class="fa fa-times text-danger"></i></button>
                                        <x-lw.input-field type="text" x-bind:value="botListMessageSection.title" id="lwListMessageSectionTitle" data-form-group-class="" :label="__tr('Section Title')" x-bind:name="'sections[' + botListMessageSection.id+'][title]'" required="true" />
                                        <template x-for="botListMessageRow in botListMessageSection.rows">
                                        <fieldset>
                                            <legend  class="py-1 px-2 mb-1"><small>{{  __tr('Row') }}</small></legend>
                                            <input type="hidden" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][id]'" x-bind:value="botListMessageRow.id" required="true">
                                            {{-- delete row btn --}}
                                            <button @click.prevent="deleteRow(botListMessageSection.id, botListMessageRow.id)" class="btn btn-link float-right p-1 mt--4" type="button"><i class="fa fa-times text-danger"></i></button>
                                            {{-- row id --}}
                                            <x-lw.input-field type="text" x-bind:id="'lwListMessageRowId'+botListMessageSection.id+botListMessageRow.id" data-form-group-class="" :label="__tr('Row ID')" x-bind:value="botListMessageRow.row_id" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][row_id]'" required="true" />
                                            {{-- row title --}}
                                            <x-lw.input-field type="text" x-bind:id="'lwListMessageRowTitle'+botListMessageSection.id+botListMessageRow.id" data-form-group-class="" :label="__tr('Row Title')" x-bind:value="botListMessageRow.title" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][title]'" required="true" />
                                            {{-- row description --}}
                                            <div class="form-group">
                                                <label for="">{{ __tr('Row Description (optional)') }}</label>
                                                <textarea class="form-control" rows="2" x-bind:id="'lwListMessageRowDescription'+botListMessageSection.id+botListMessageRow.id" x-bind:value="botListMessageRow.description" x-bind:name="'sections[' + botListMessageSection.id+'][rows]['+botListMessageRow.id+'][description]'"></textarea>
                                            </div>
                                        </fieldset>
                                    </template>
                                    <button type="button" class="btn btn-sm btn-light my-2" @click="addListSectionRow(botListMessageSection.id)">{{  __tr('Add Row') }}</button>
                                    </fieldset>
                                </template>
                                <button type="button" class="btn btn-sm btn-dark my-2" @click="addListSection()">{{  __tr('Add Section') }}</button>
                            </div>
                        <% } else if(__tData.__data?.interaction_message.interactive_type && __tData.__data?.interaction_message.interactive_type == 'cta_url') { %>
                            <fieldset>
                                <legend>{{  __tr('Call to Action (CTA) URL Button') }}</legend>
                                <x-lw.input-field type="text" id="lwCtaEditUrlButtonDisplayText" data-form-group-class="" :label="__tr('CTA Button Display Text')" name="button_display_text" value="<%- __tData.__data?.interaction_message.cta_url?.display_text %>" required="true"/>
                                <x-lw.input-field type="text" id="lwCtaEditButtonUrl" data-form-group-class="" :label="__tr('CTA Button URL')" name="button_url" value="<%- __tData.__data?.interaction_message.cta_url?.url %>" required="true" />
                            </fieldset>
                        <% } else { %>
                        <fieldset>
                            <legend>{{  __tr('Reply Buttons') }}</legend>
                            <x-lw.input-field type="text" id="lwAdvanceEditButton1" data-form-group-class="" :label="__tr('Button 1 Label')" name="buttons[1]" value="<%- __tData.__data?.interaction_message.buttons[1] %>" required="true" />
                            <x-lw.input-field type="text" id="lwAdvanceEditButton2" data-form-group-class="" :label="__tr('Button 2 Label (optional)')" name="buttons[2]" value="<%- __tData.__data?.interaction_message.buttons[2] %>" />
                            <x-lw.input-field type="text" id="lwAdvanceEditButton3" data-form-group-class="" :label="__tr('Button 3 Label (optional)')" name="buttons[3]" value="<%- __tData.__data?.interaction_message.buttons[3] %>" />
                        </fieldset>
                        <% } %>
            {{-- footer text --}}
            <x-lw.input-field type="text" id="lwAdvanceEditFooterText" data-form-group-class=""
            :label="__tr('Footer Text (optional)')" name="footer_text" value="<%- __tData.__data?.interaction_message.footer_text %>"  />
            </fieldset>
            {{-- /reply --}}
            </fieldset>
            <% } else { %>
                <input type="hidden" name="message_type" value="simple">
            <% } %>
            </fieldset>
                <template x-if="!botFlowUid">
                    <div x-data="{botTriggerType:'<%- __tData.trigger_type %>'}">
                        <!-- Trigger_Type -->
                        <x-lw.input-field class="" type="selectize" x-model="botTriggerType" id="lwTriggerTypeEditField" data-form-group-class="" data-selected="<%- __tData.trigger_type %>" :label="__tr('Trigger Type')" name="trigger_type"  required="true">
                        <x-slot name="selectOptions">
                            <option value="">{{ __tr('How do you want to trigger this message?') }}</option>
                            @foreach (configItem('bot_reply_trigger_types') as $replyBotTypeKey => $replyBotType)
                                <option value="{{ $replyBotTypeKey }}">{{ $replyBotType['title'] }} </option>
                                @endforeach
                            </x-slot>
                        </x-lw.input-field>
                        <!-- /Trigger_Type -->
                        @foreach (configItem('bot_reply_trigger_types') as $replyBotTypeKey => $replyBotType)
                        <template x-if="botTriggerType == '{{ $replyBotTypeKey }}'">
                            <div x-show="botTriggerType == '{{ $replyBotTypeKey }}'" class="alert alert-dark">{{ $replyBotType['description'] }}</div>
                        </template>
                        @endforeach
                        <!-- Reply_Trigger -->
                        <template x-if="botTriggerType != 'welcome'">
                            <div>
                                <x-lw.input-field type="text" id="lwReplyTriggerEditField" data-form-group-class="" :label="__tr('Reply Trigger Subject')" value="<%- __tData.reply_trigger %>" name="reply_trigger"  required="true"/>
                                    <div><small class="text-muted">{{  __tr('You can have comma separated multiple triggers.') }}</small></div>
                            </div>
                        </template>
                    </div>
                </template>
                @if (!isset($botFlowUid) or !$botFlowUid)
                <div class="form-group pt-3">
                    <input type="checkbox" id="lwEditBotStatus" <%- (!__tData.status || (__tData.status == 2)) ? '' : 'checked' %> data-lw-plugin="lwSwitchery" value="1" name="status">
                    <label for="lwEditBotStatus">{{  __tr('Status') }}</label>
                </div>
                @endif
            </div>
                <!-- /Reply_Trigger -->
                <div class="my-4">
                    <x-lw.checkbox id="lwEditValidateBotReply" :offValue="0" name="validate_bot_reply" value="1" data-lw-plugin="lwSwitchery" :label="__tr('Validate Bot Reply by Sending Test Message')" />
                </div>
                    </script>
                <!-- form footer -->
                <div class="modal-footer">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">{{ __tr('Submit') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __tr('Close') }}</button>
                </div>
    </x-lw.form>
    <!--/  Edit Bot Reply Form -->
</x-lw.modal>
<!--/ Edit Bot Reply Modal -->