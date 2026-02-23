<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{if $xm_is_edit}><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_LIST}> &rsaquo; Edit<{else}><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_ADD}><{/if}></h1>

  <form method="post" action="modules.php?op=save" class="xm-admin-form" novalidate>
    <input type="hidden" name="token"     value="<{$xm_token}>">
    <input type="hidden" name="module_id" value="<{$xm_module.module_id|escape:'html'}>">

    <div class="xm-form-grid">
      <div class="xm-form-group">
        <label class="xm-label" for="dirname"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_DIRNAME}> <span aria-hidden="true">*</span></label>
        <input type="text" id="dirname" name="dirname" class="xm-input" required
               value="<{$xm_module.dirname|escape}>" maxlength="50"
               <{if $xm_is_edit}>readonly<{/if}>>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="name"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_NAME}> <span aria-hidden="true">*</span></label>
        <input type="text" id="name" name="name" class="xm-input" required
               value="<{$xm_module.name|escape}>" maxlength="150">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="webhook_url"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_WEBHOOK}></label>
        <input type="url" id="webhook_url" name="webhook_url" class="xm-input"
               value="<{$xm_module.webhook_url|escape}>" maxlength="500">
      </div>
    </div>

    <div class="xm-form-group">
      <label class="xm-label" for="description"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_DESC}></label>
      <textarea id="description" name="description" class="xm-input xm-textarea" rows="3"><{$xm_module.description|escape}></textarea>
    </div>

    <{if $xm_is_edit && $xm_module.api_key}>
    <div class="xm-form-group">
      <label class="xm-label"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_API_KEY}></label>
      <input type="text" class="xm-input" value="<{$xm_module.api_key|escape}>" readonly
             onclick="this.select()" title="Click to select">
      <small class="xm-hint">Use this key in your third-party module configuration.</small>
    </div>
    <{/if}>

    <{if $xm_plans}>
    <div class="xm-form-group">
      <label class="xm-label"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_RULES}></label>
      <div class="xm-table-wrap">
        <table class="xm-table xm-table--compact">
          <thead>
            <tr>
              <th>Plan</th>
              <th>Grant Access</th>
              <th>Access Type</th>
              <th>Limit Value</th>
            </tr>
          </thead>
          <tbody>
            <{foreach item=p from=$xm_plans}>
            <tr>
              <td><{$p.name|escape}></td>
              <td>
                <input type="checkbox" name="rule_plan_ids[]" value="<{$p.plan_id}>"
                       <{if isset($xm_rules[$p.plan_id])}> checked<{/if}>>
              </td>
              <td>
                <select name="access_type_<{$p.plan_id}>" class="xm-input xm-input--sm">
                  <option value="full"<{if isset($xm_rules[$p.plan_id]) && $xm_rules[$p.plan_id].access_type eq 'full'}> selected<{/if}>>Full</option>
                  <option value="limited"<{if isset($xm_rules[$p.plan_id]) && $xm_rules[$p.plan_id].access_type eq 'limited'}> selected<{/if}>>Limited</option>
                  <option value="metered"<{if isset($xm_rules[$p.plan_id]) && $xm_rules[$p.plan_id].access_type eq 'metered'}> selected<{/if}>>Metered</option>
                </select>
              </td>
              <td>
                <input type="number" name="limit_value_<{$p.plan_id}>" class="xm-input xm-input--sm"
                       min="0" value="<{if isset($xm_rules[$p.plan_id])}><{$xm_rules[$p.plan_id].limit_value}><{else}>0<{/if}>">
              </td>
            </tr>
            <{/foreach}>
          </tbody>
        </table>
      </div>
    </div>
    <{/if}>

    <div class="xm-form-checkboxes">
      <label class="xm-checkbox-label">
        <input type="checkbox" name="is_active" value="1"<{if $xm_module.is_active|default:1}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ACTIVE}>
      </label>
    </div>

    <div class="xm-form-actions">
      <button type="submit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_SAVE}></button>
      <a href="modules.php" class="xm-btn xm-btn--text"><{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}></a>
    </div>
  </form>
</div>
