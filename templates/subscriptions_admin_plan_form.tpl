<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{if $xm_is_edit}><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_EDIT}><{else}><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ADD}><{/if}></h1>

  <form method="post" action="<{$xm_form_action}>" class="xm-admin-form" novalidate>
    <input type="hidden" name="token"   value="<{$xm_token}>">
    <input type="hidden" name="plan_id" value="<{$xm_plan.plan_id|escape:'html'}>">

    <div class="xm-form-grid">
      <div class="xm-form-group">
        <label class="xm-label" for="name"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_NAME}> <span aria-hidden="true">*</span></label>
        <input type="text" id="name" name="name" class="xm-input" required
               value="<{$xm_plan.name|escape}>" maxlength="150">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="price"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_PRICE}> <span aria-hidden="true">*</span></label>
        <input type="number" id="price" name="price" class="xm-input" required
               step="0.01" min="0" value="<{$xm_plan.price|default:'0.00'}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="currency">Currency</label>
        <select id="currency" name="currency" class="xm-input">
          <{foreach item=c from=['USD','EUR','GBP','CAD','AUD']}>
          <option value="<{$c}>"<{if $xm_plan.currency eq $c}> selected<{/if}>><{$c}></option>
          <{/foreach}>
        </select>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="billing_cycle"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_CYCLE}></label>
        <select id="billing_cycle" name="billing_cycle" class="xm-input">
          <{foreach item=c from=['one_time','daily','weekly','monthly','quarterly','annual']}>
          <option value="<{$c}>"<{if $xm_plan.billing_cycle eq $c}> selected<{/if}>><{$c|capitalize}></option>
          <{/foreach}>
        </select>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="trial_days"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_TRIAL}></label>
        <input type="number" id="trial_days" name="trial_days" class="xm-input"
               min="0" value="<{$xm_plan.trial_days|default:0}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="setup_fee"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_SETUP}></label>
        <input type="number" id="setup_fee" name="setup_fee" class="xm-input"
               step="0.01" min="0" value="<{$xm_plan.setup_fee|default:'0.00'}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="discount_annual"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_DISCOUNT}></label>
        <input type="number" id="discount_annual" name="discount_annual" class="xm-input"
               step="0.01" min="0" max="100" value="<{$xm_plan.discount_annual|default:'0.00'}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="pricing_model"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_MODEL}></label>
        <select id="pricing_model" name="pricing_model" class="xm-input">
          <option value="flat"<{if $xm_plan.pricing_model eq 'flat'}> selected<{/if}>>Flat</option>
          <option value="usage"<{if $xm_plan.pricing_model eq 'usage'}> selected<{/if}>>Usage-Based</option>
          <option value="hybrid"<{if $xm_plan.pricing_model eq 'hybrid'}> selected<{/if}>>Hybrid</option>
        </select>
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="sort_order"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ORDER}></label>
        <input type="number" id="sort_order" name="sort_order" class="xm-input"
               min="0" value="<{$xm_plan.sort_order|default:0}>">
      </div>

      <div class="xm-form-group">
        <label class="xm-label" for="max_users"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_MAX_USERS}></label>
        <input type="number" id="max_users" name="max_users" class="xm-input"
               min="0" value="<{$xm_plan.max_users|default:0}>">
      </div>
    </div>

    <div class="xm-form-group">
      <label class="xm-label" for="description"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_DESC}></label>
      <textarea id="description" name="description" class="xm-input xm-textarea" rows="4"><{$xm_plan.description|escape}></textarea>
    </div>

    <div class="xm-form-group">
      <label class="xm-label" for="features"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_FEATURES}></label>
      <textarea id="features" name="features" class="xm-input xm-textarea" rows="6"
                placeholder="Feature 1&#10;Feature 2&#10;Feature 3"><{$xm_features_text|escape}></textarea>
    </div>

    <div class="xm-form-checkboxes">
      <label class="xm-checkbox-label">
        <input type="checkbox" name="is_featured" value="1"<{if $xm_plan.is_featured}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_PLAN_FEATURED}>
      </label>
      <label class="xm-checkbox-label">
        <input type="checkbox" name="is_active" value="1"<{if $xm_plan.is_active|default:1}> checked<{/if}>>
        <{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ACTIVE}>
      </label>
    </div>

    <div class="xm-form-actions">
      <button type="submit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_SAVE}></button>
      <a href="plans.php" class="xm-btn xm-btn--text"><{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}></a>
    </div>
  </form>
</div>
