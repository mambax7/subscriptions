<div class="subscriptions-block subscriptions-block-plans">
  <{if $block.plans}>
  <ul class="xm-block-plan-list">
    <{foreach item=plan from=$block.plans}>
    <li class="xm-block-plan-item">
      <a href="<{$plan.checkout_url}>" class="xm-block-plan-link">
        <span class="xm-block-plan-name"><{$plan.name|escape}></span>
        <span class="xm-block-plan-price"><{$plan.price}> / <{$plan.billing_cycle|escape}></span>
        <{if $plan.trial_days gt 0}>
        <span class="xm-block-plan-trial"><{$plan.trial_days}>-day free trial</span>
        <{/if}>
      </a>
    </li>
    <{/foreach}>
  </ul>
  <a href="<{$block.plans_url}>" class="xm-block-plans-more">View all plans &raquo;</a>
  <{else}>
  <p class="xm-block-empty">No plans available.</p>
  <{/if}>
</div>
