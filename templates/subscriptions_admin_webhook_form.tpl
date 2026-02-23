<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{if $xm_is_edit}><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_LIST}> &rsaquo; Edit<{else}><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_ADD}><{/if}></h1>

  <form method="post" action="webhooks.php?op=save" class="xm-admin-form" novalidate>
    <input type="hidden" name="token"      value="<{$xm_token}>">
    <input type="hidden" name="webhook_id" value="<{$xm_webhook.webhook_id|escape:'html'}>">

    <div class="xm-form-group">
      <label class="xm-label" for="url"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_URL}> <span aria-hidden="true">*</span></label>
      <input type="url" id="url" name="url" class="xm-input" required
             value="<{$xm_webhook.url|escape}>" maxlength="500"
             placeholder="https://example.com/webhook">
    </div>

    <{if $xm_is_edit && $xm_webhook.secret}>
    <div class="xm-form-group">
      <label class="xm-label"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_SECRET}></label>
      <input type="text" class="xm-input" value="<{$xm_webhook.secret|escape}>" readonly
             onclick="this.select()" title="Click to select">
      <small class="xm-hint">Use this secret to verify webhook signatures (HMAC-SHA256).</small>
    </div>
    <{/if}>

    <div class="xm-form-group">
      <label class="xm-label"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_EVENTS}></label>
      <div class="xm-checkbox-group">
        <{foreach item=event from=$xm_all_events}>
        <label class="xm-checkbox-label">
          <input type="checkbox" name="events[]" value="<{$event|escape}>"
                 <{if in_array($event, $xm_selected_events)}> checked<{/if}>>
          <code><{$event|escape}></code>
        </label>
        <{/foreach}>
      </div>
    </div>

    <div class="xm-form-checkboxes">
      <label class="xm-checkbox-label">
        <input type="checkbox" name="is_active" value="1"<{if $xm_webhook.is_active|default:1}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ACTIVE}>
      </label>
    </div>

    <div class="xm-form-actions">
      <button type="submit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_SAVE}></button>
      <a href="webhooks.php" class="xm-btn xm-btn--text"><{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}></a>
    </div>
  </form>
</div>
