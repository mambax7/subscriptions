<div class="subscriptions-admin">
  <div class="xm-admin-header">
    <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_GATEWAY_LIST}></h1>
  </div>

  <!-- Gateway tabs -->
  <nav class="xm-tabs" aria-label="Select payment gateway">
    <{foreach item=gw from=$xm_gateways}>
    <a href="gateways.php?gateway=<{$gw}>"
       class="xm-tab<{if $xm_gateway eq $gw}> xm-tab--active<{/if}>"
       role="tab" aria-selected="<{if $xm_gateway eq $gw}>true<{else}>false<{/if}>">
       <{$gw|capitalize}>
    </a>
    <{/foreach}>
  </nav>

  <form method="post" action="gateways.php?op=save" class="xm-admin-form">
    <input type="hidden" name="token"   value="<{$xm_token}>">
    <input type="hidden" name="gateway" value="<{$xm_gateway|escape}>">

    <{if $xm_gateway eq 'paypal'}>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_business_email"><{$smarty.const._AM_SUBSCRIPTIONS_BUSINESS_EMAIL}></label>
      <input type="email" id="gw_business_email" name="gw_business_email" class="xm-input"
             value="<{$xm_config.business_email|default:''|escape}>">
    </div>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_api_username">API Username</label>
      <input type="text" id="gw_api_username" name="gw_api_username" class="xm-input"
             value="<{$xm_config.api_username|default:''|escape}>" autocomplete="off">
    </div>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_api_password">API Password</label>
      <input type="password" id="gw_api_password" name="gw_api_password" class="xm-input"
             value="<{$xm_config.api_password|default:''|escape}>" autocomplete="new-password">
    </div>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_api_signature">API Signature</label>
      <input type="password" id="gw_api_signature" name="gw_api_signature" class="xm-input"
             value="<{$xm_config.api_signature|default:''|escape}>" autocomplete="new-password">
    </div>

    <{elseif $xm_gateway eq 'stripe'}>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_public_key">Publishable Key</label>
      <input type="text" id="gw_public_key" name="gw_public_key" class="xm-input"
             value="<{$xm_config.public_key|default:''|escape}>" autocomplete="off">
    </div>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_secret_key"><{$smarty.const._AM_SUBSCRIPTIONS_API_KEY}></label>
      <input type="password" id="gw_secret_key" name="gw_secret_key" class="xm-input"
             value="<{$xm_config.secret_key|default:''|escape}>" autocomplete="new-password">
    </div>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_webhook_secret">Webhook Secret</label>
      <input type="password" id="gw_webhook_secret" name="gw_webhook_secret" class="xm-input"
             value="<{$xm_config.webhook_secret|default:''|escape}>" autocomplete="new-password">
    </div>

    <{elseif $xm_gateway eq 'manual'}>
    <div class="xm-form-group">
      <label class="xm-label" for="gw_instructions">Payment Instructions</label>
      <textarea id="gw_instructions" name="gw_instructions" class="xm-input xm-textarea" rows="6"><{$xm_config.instructions|default:''|escape}></textarea>
    </div>
    <{/if}>

    <div class="xm-form-group">
      <label class="xm-checkbox-label">
        <input type="checkbox" name="gw_sandbox" value="1"
               <{if $xm_config.sandbox|default:1}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_SANDBOX_MODE}>
      </label>
    </div>

    <div class="xm-form-actions">
      <button type="submit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_SAVE}></button>
    </div>
  </form>
</div>
