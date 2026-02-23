<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{if $xm_is_edit}><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_LIST}> &rsaquo; Edit<{else}><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_ADD}><{/if}></h1>

  <form method="post" action="coupons.php?op=save" class="xm-admin-form" novalidate>
    <input type="hidden" name="token"     value="<{$xm_token}>">
    <input type="hidden" name="coupon_id" value="<{$xm_coupon.coupon_id|escape:'html'}>">

    <div class="xm-form-grid">
      <div class="xm-form-group">
        <label class="xm-label" for="code"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_CODE}> <span aria-hidden="true">*</span></label>
        <input type="text" id="code" name="code" class="xm-input" required
               value="<{$xm_coupon.code|escape}>" maxlength="50" style="text-transform:uppercase">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="name"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_NAME}></label>
        <input type="text" id="name" name="name" class="xm-input"
               value="<{$xm_coupon.name|escape}>" maxlength="150">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="discount_type"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_TYPE}></label>
        <select id="discount_type" name="discount_type" class="xm-input">
          <option value="percentage"<{if $xm_coupon.discount_type eq 'percentage'}> selected<{/if}>>Percentage (%)</option>
          <option value="fixed"<{if $xm_coupon.discount_type eq 'fixed'}> selected<{/if}>>Fixed Amount</option>
        </select>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="discount_value"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_VALUE}></label>
        <input type="number" id="discount_value" name="discount_value" class="xm-input"
               step="0.01" min="0" value="<{$xm_coupon.discount_value|default:'0.00'}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="min_amount"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_MIN}></label>
        <input type="number" id="min_amount" name="min_amount" class="xm-input"
               step="0.01" min="0" value="<{$xm_coupon.min_amount|default:'0.00'}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="max_uses"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_USES}> (0 = unlimited)</label>
        <input type="number" id="max_uses" name="max_uses" class="xm-input"
               min="0" value="<{$xm_coupon.max_uses|default:0}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="max_uses_per_user">Max Uses Per User</label>
        <input type="number" id="max_uses_per_user" name="max_uses_per_user" class="xm-input"
               min="1" value="<{$xm_coupon.max_uses_per_user|default:1}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="recurring_months"><{$smarty.const._MD_SUBSCRIPTIONS_COUPON_RECURRING_MONTHS}></label>
        <input type="number" id="recurring_months" name="recurring_months" class="xm-input"
               min="-1" value="<{$xm_coupon.recurring_months|default:0}>">
        <span class="xm-field-hint"><{$smarty.const._MD_SUBSCRIPTIONS_COUPON_RECURRING_HELP}></span>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="valid_from"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_VALID}> (from)</label>
        <input type="date" id="valid_from" name="valid_from" class="xm-input"
               value="<{if $xm_coupon.valid_from}><{$xm_coupon.valid_from|date_format:'%Y-%m-%d'}><{/if}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="valid_until"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_VALID}> (until)</label>
        <input type="date" id="valid_until" name="valid_until" class="xm-input"
               value="<{if $xm_coupon.valid_until}><{$xm_coupon.valid_until|date_format:'%Y-%m-%d'}><{/if}>">
      </div>
    </div>

    <{if $xm_plans}>
    <div class="xm-form-group">
      <label class="xm-label">Restrict to Plans (leave empty for all plans)</label>
      <div class="xm-checkbox-group">
        <{foreach item=p from=$xm_plans}>
        <label class="xm-checkbox-label">
          <input type="checkbox" name="plan_ids[]" value="<{$p.plan_id}>"
                 <{if in_array($p.plan_id, $xm_selected_plan_ids|default:[])}> checked<{/if}>>
          <{$p.name|escape}>
        </label>
        <{/foreach}>
      </div>
    </div>
    <{/if}>

    <div class="xm-form-checkboxes">
      <label class="xm-checkbox-label">
        <input type="checkbox" name="is_active" value="1"<{if $xm_coupon.is_active|default:1}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ACTIVE}>
      </label>
    </div>

    <div class="xm-form-actions">
      <button type="submit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_SAVE}></button>
      <a href="coupons.php" class="xm-btn xm-btn--text"><{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}></a>
    </div>
  </form>
</div>
