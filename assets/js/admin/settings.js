(()=>{"use strict";!function(t){function e(t,e){var s=null;return function(){var a=this,n=arguments;clearTimeout(s),s=setTimeout((function(){t.apply(a,n)}),e)}}t("#wta-category-search, #wta-tag-search").on("keyup",e((function(){let e=t(this),s=e.attr("data-taxonomy"),a=e.closest(".wta-inclusion-selector"),n=e.attr("data-type");a.find(".wta-loader").show(),a.find(".wta-component-no-results").hide();let i=a.find(".barn2-search-list__list");const l=e.val();if(!l&&!l.length)return void a.find(".wta-loader").hide();let r=new URLSearchParams({search:l});wp.apiFetch({path:`/wc/v3/products/${s}/?${r.toString()}`}).then((t=>{if(e.closest(".wta-inclusion-selector").find(".wta-loader").hide(),0==t.length)return void e.closest(".wta-inclusion-selector").find(".wta-component-no-results").show();let s="";t.map((t=>{s+=`<li data-inclusion-id=${t.id} data-inclusion-name="${t.name}" data-inclusion-type="${n}"><label for="search-list-item-${n}-0-${t.id}" data-inclusion-type="${n}" class=" barn2-search-list__item depth-0"><input type="checkbox" id="search-list-item-${n}-0-${t.id}" name="search-list-item-${n}-0" class="barn2-search-list__item-input" value="">\t<span class="barn2-search-list__item-label"><span class="barn2-search-list__item-name">${t.name}</span></span></label></li>`})),i.html(s).show()}))}),500)),t(".wta-visibility_condition").on("change",(function(){"yes"===t(this).val()?t("#inclusions-list.form-table").addClass("hide-section"):t("#inclusions-list.form-table").removeClass("hide-section")})),t(document).on("click",".barn2-search-list__list li",e((function(){const e=t(this),s=e.closest(".wta-inclusion-selector"),a=e.attr("data-inclusion-id"),n=e.attr("data-inclusion-name"),i=e.attr("data-inclusion-type"),l=s.find('.barn2-search-list__selected_terms input[type="hidden"]');if(Array.from(l,(t=>t.value)).includes(a))return;let r=`<li><span class="barn2-selected-list__tag"><span class="barn2-tag__text" id="barn2-tag__label-${a}"><span class="screen-reader-text">${n}</span><span aria-hidden="true">${n}</span></span><input type="hidden" name="wpt_${i}_list[]" value="${a}"><button type="button" aria-describedby="barn2-tag__label-${a}" class="components-button barn2-tag__remove" id="barn2-remove-term" aria-label="${n}"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="clear-icon" aria-hidden="true" focusable="false"><path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21ZM15.5303 8.46967C15.8232 8.76256 15.8232 9.23744 15.5303 9.53033L13.0607 12L15.5303 14.4697C15.8232 14.7626 15.8232 15.2374 15.5303 15.5303C15.2374 15.8232 14.7626 15.8232 14.4697 15.5303L12 13.0607L9.53033 15.5303C9.23744 15.8232 8.76256 15.8232 8.46967 15.5303C8.17678 15.2374 8.17678 14.7626 8.46967 14.4697L10.9393 12L8.46967 9.53033C8.17678 9.23744 8.17678 8.76256 8.46967 8.46967C8.76256 8.17678 9.23744 8.17678 9.53033 8.46967L12 10.9393L14.4697 8.46967C14.7626 8.17678 15.2374 8.17678 15.5303 8.46967Z"></path></svg></button></span></li>`;s.find(".barn2-search-list__selected").removeClass("wpt-hide-selected-terms-section"),s.find(".barn2-search-list__selected").show(),s.find(".barn2-search-list__selected_terms").append(r)}),50)),t(document).on("click","#barn2-remove-term",(function(){t(this).closest("li").remove()})),t(".barn2-remove-inclusions").on("click",(function(){const e=t(this).closest(".wta-inclusion-selector");e.find(".barn2-search-list__selected_terms").empty(),e.find(".barn2-search-list__selected").hide()})),t("body.post-type-woo_product_tab .wrap .subsubsub").html('<p class="wta-sub-heading">Create additional tabs for your product pages and choose which categories they appear on. For more options,<a target="_blank" href="https://barn2.com/wordpress-plugins/woocommerce-product-tabs/?utm_source=settings&utm_medium=settings&utm_campaign=settingsinline&utm_content=wta-settings">upgrade to Pro.</a></p>')}(jQuery)})();