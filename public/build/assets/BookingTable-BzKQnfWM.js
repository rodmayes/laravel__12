import{h as H,B as N,i as F,R as j,E as U,_ as V,k as Z,$ as q,a0 as J,p as M,a1 as S,a2 as Y,Q as G,q as L,v as Q,x as W,W as X,c as g,o as p,w as l,a as s,A as $,m as C,H as _,g as D,f as h,y as x,F as ee,G as te,e as b,t as m,u as w,b as u,n as K,J as E,K as R}from"./app-ZT2Cvq7Q.js";import{O as y,C as ne}from"./index-B6rpEtJk.js";import{F as oe,s as ie}from"./index-XZZn8wGU.js";import{a as re,s as se}from"./index-Dufz52zH.js";import{s as ae}from"./index-DPTf6-gk.js";import{s as le}from"./index-BqnOiHH4.js";import{s as ce}from"./index-BByHElJD.js";import{c as O,t as de,a as ue,p as pe}from"./useFormatters-DGNKrJCF.js";import"./index-B_I2eUS1.js";import"./index-CYo_M90q.js";import"./index-D27q2tvh.js";import"./index-BdmbPcsQ.js";import"./index-DwbX7Vea.js";import"./index-LvVMXtpy.js";import"./index-Cjv_SjRQ.js";import"./index-BNSbANZM.js";import"./index-D2RD8szz.js";import"./index-BIZG_u5S.js";import"./index-Dw_XeGju.js";import"./index-CBDkM6LP.js";function fe(t,...e){const n=O.bind(null,e.find(a=>typeof a=="object"));return e.map(n)}function T(t,e){const n=de(t,e==null?void 0:e.in);return n.setHours(0,0,0,0),n}function ve(t){return O(t,Date.now())}function me(t,e,n){const[a,v]=fe(n==null?void 0:n.in,t,e);return+T(a)==+T(v)}function he(t,e){return me(O((e==null?void 0:e.in)||t,t),ve((e==null?void 0:e.in)||t))}var be=H`
    .p-popover {
        margin-block-start: dt('popover.gutter');
        background: dt('popover.background');
        color: dt('popover.color');
        border: 1px solid dt('popover.border.color');
        border-radius: dt('popover.border.radius');
        box-shadow: dt('popover.shadow');
    }

    .p-popover-content {
        padding: dt('popover.content.padding');
    }

    .p-popover-flipped {
        margin-block-start: calc(dt('popover.gutter') * -1);
        margin-block-end: dt('popover.gutter');
    }

    .p-popover-enter-from {
        opacity: 0;
        transform: scaleY(0.8);
    }

    .p-popover-leave-to {
        opacity: 0;
    }

    .p-popover-enter-active {
        transition:
            transform 0.12s cubic-bezier(0, 0, 0.2, 1),
            opacity 0.12s cubic-bezier(0, 0, 0.2, 1);
    }

    .p-popover-leave-active {
        transition: opacity 0.1s linear;
    }

    .p-popover:after,
    .p-popover:before {
        bottom: 100%;
        left: calc(dt('popover.arrow.offset') + dt('popover.arrow.left'));
        content: ' ';
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .p-popover:after {
        border-width: calc(dt('popover.gutter') - 2px);
        margin-left: calc(-1 * (dt('popover.gutter') - 2px));
        border-style: solid;
        border-color: transparent;
        border-bottom-color: dt('popover.background');
    }

    .p-popover:before {
        border-width: dt('popover.gutter');
        margin-left: calc(-1 * dt('popover.gutter'));
        border-style: solid;
        border-color: transparent;
        border-bottom-color: dt('popover.border.color');
    }

    .p-popover-flipped:after,
    .p-popover-flipped:before {
        bottom: auto;
        top: 100%;
    }

    .p-popover.p-popover-flipped:after {
        border-bottom-color: transparent;
        border-top-color: dt('popover.background');
    }

    .p-popover.p-popover-flipped:before {
        border-bottom-color: transparent;
        border-top-color: dt('popover.border.color');
    }
`,ye={root:"p-popover p-component",content:"p-popover-content"},ge=N.extend({name:"popover",style:be,classes:ye}),ke={name:"BasePopover",extends:U,props:{dismissable:{type:Boolean,default:!0},appendTo:{type:[String,Object],default:"body"},baseZIndex:{type:Number,default:0},autoZIndex:{type:Boolean,default:!0},breakpoints:{type:Object,default:null},closeOnEscape:{type:Boolean,default:!0}},style:ge,provide:function(){return{$pcPopover:this,$parentInstance:this}}},z={name:"Popover",extends:ke,inheritAttrs:!1,emits:["show","hide"],data:function(){return{visible:!1}},watch:{dismissable:{immediate:!0,handler:function(e){e?this.bindOutsideClickListener():this.unbindOutsideClickListener()}}},selfClick:!1,target:null,eventTarget:null,outsideClickListener:null,scrollHandler:null,resizeListener:null,container:null,styleElement:null,overlayEventListener:null,documentKeydownListener:null,beforeUnmount:function(){this.dismissable&&this.unbindOutsideClickListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.destroyStyle(),this.unbindResizeListener(),this.target=null,this.container&&this.autoZIndex&&L.clear(this.container),this.overlayEventListener&&(y.off("overlay-click",this.overlayEventListener),this.overlayEventListener=null),this.container=null},mounted:function(){this.breakpoints&&this.createStyle()},methods:{toggle:function(e,n){this.visible?this.hide():this.show(e,n)},show:function(e,n){this.visible=!0,this.eventTarget=e.currentTarget,this.target=n||e.currentTarget},hide:function(){this.visible=!1},onContentClick:function(){this.selfClick=!0},onEnter:function(e){var n=this;Q(e,{position:"absolute",top:"0"}),this.alignOverlay(),this.dismissable&&this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.autoZIndex&&L.set("overlay",e,this.baseZIndex+this.$primevue.config.zIndex.overlay),this.overlayEventListener=function(a){n.container.contains(a.target)&&(n.selfClick=!0)},this.focus(),y.on("overlay-click",this.overlayEventListener),this.$emit("show"),this.closeOnEscape&&this.bindDocumentKeyDownListener()},onLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.unbindDocumentKeyDownListener(),y.off("overlay-click",this.overlayEventListener),this.overlayEventListener=null,this.$emit("hide")},onAfterLeave:function(e){this.autoZIndex&&L.clear(e)},alignOverlay:function(){M(this.container,this.target,!1);var e=S(this.container),n=S(this.target),a=0;e.left<n.left&&(a=n.left-e.left),this.container.style.setProperty(Y("popover.arrow.left").name,"".concat(a,"px")),e.top<n.top&&(this.container.setAttribute("data-p-popover-flipped","true"),!this.isUnstyled&&G(this.container,"p-popover-flipped"))},onContentKeydown:function(e){e.code==="Escape"&&this.closeOnEscape&&(this.hide(),J(this.target))},onButtonKeydown:function(e){switch(e.code){case"ArrowDown":case"ArrowUp":case"ArrowLeft":case"ArrowRight":e.preventDefault()}},focus:function(){var e=this.container.querySelector("[autofocus]");e&&e.focus()},onKeyDown:function(e){e.code==="Escape"&&this.closeOnEscape&&(this.visible=!1)},bindDocumentKeyDownListener:function(){this.documentKeydownListener||(this.documentKeydownListener=this.onKeyDown.bind(this),window.document.addEventListener("keydown",this.documentKeydownListener))},unbindDocumentKeyDownListener:function(){this.documentKeydownListener&&(window.document.removeEventListener("keydown",this.documentKeydownListener),this.documentKeydownListener=null)},bindOutsideClickListener:function(){var e=this;!this.outsideClickListener&&q()&&(this.outsideClickListener=function(n){e.visible&&!e.selfClick&&!e.isTargetClicked(n)&&(e.visible=!1),e.selfClick=!1},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null,this.selfClick=!1)},bindScrollListener:function(){var e=this;this.scrollHandler||(this.scrollHandler=new ne(this.target,function(){e.visible&&(e.visible=!1)})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var e=this;this.resizeListener||(this.resizeListener=function(){e.visible&&!Z()&&(e.visible=!1)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},isTargetClicked:function(e){return this.eventTarget&&(this.eventTarget===e.target||this.eventTarget.contains(e.target))},containerRef:function(e){this.container=e},createStyle:function(){if(!this.styleElement&&!this.isUnstyled){var e;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",V(this.styleElement,"nonce",(e=this.$primevue)===null||e===void 0||(e=e.config)===null||e===void 0||(e=e.csp)===null||e===void 0?void 0:e.nonce),document.head.appendChild(this.styleElement);var n="";for(var a in this.breakpoints)n+=`
                        @media screen and (max-width: `.concat(a,`) {
                            .p-popover[`).concat(this.$attrSelector,`] {
                                width: `).concat(this.breakpoints[a],` !important;
                            }
                        }
                    `);this.styleElement.innerHTML=n}},destroyStyle:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)},onOverlayClick:function(e){y.emit("overlay-click",{originalEvent:e,target:this.target})}},directives:{focustrap:oe,ripple:j},components:{Portal:F}},Le=["aria-modal"];function Ce(t,e,n,a,v,i){var r=W("Portal"),k=X("focustrap");return p(),g(r,{appendTo:t.appendTo},{default:l(function(){return[s($,C({name:"p-popover",onEnter:i.onEnter,onLeave:i.onLeave,onAfterLeave:i.onAfterLeave},t.ptm("transition")),{default:l(function(){return[v.visible?_((p(),h("div",C({key:0,ref:i.containerRef,role:"dialog","aria-modal":v.visible,onClick:e[3]||(e[3]=function(){return i.onOverlayClick&&i.onOverlayClick.apply(i,arguments)}),class:t.cx("root")},t.ptmi("root")),[t.$slots.container?x(t.$slots,"container",{key:0,closeCallback:i.hide,keydownCallback:function(c){return i.onButtonKeydown(c)}}):(p(),h("div",C({key:1,class:t.cx("content"),onClick:e[0]||(e[0]=function(){return i.onContentClick&&i.onContentClick.apply(i,arguments)}),onMousedown:e[1]||(e[1]=function(){return i.onContentClick&&i.onContentClick.apply(i,arguments)}),onKeydown:e[2]||(e[2]=function(){return i.onContentKeydown&&i.onContentKeydown.apply(i,arguments)})},t.ptm("content")),[x(t.$slots,"default")],16))],16,Le)),[[k]]):D("",!0)]}),_:3},16,["onEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo"])}z.render=Ce;const we={class:"flex justify-between"},Ee={class:"text-xl font-bold"},De={class:"px-3 py-2"},Me={__name:"BookingTable",props:{items:Object,onPageChange:Function,onSortChange:Function,onRefresh:Function,deleteDialog:Object,data:Object,deleteData:Function,router:Object,can:Function},setup(t){const e=t,n=ee(),a=te({booking:null}),v=i=>n.value.toggle(i);return(i,r)=>{const k=ce,f=le,c=re,P=ae,B=se,A=ie,I=z;return p(),h(E,null,[s(B,{value:t.items.data,paginator:"",lazy:"",rows:t.items.per_page,totalRecords:t.items.total,first:(t.items.current_page-1)*t.items.per_page,rowsPerPageOptions:[5,10,20,50],sortMode:"multiple",onPage:t.onPageChange,onSort:t.onSortChange},{header:l(()=>[u("div",we,[u("span",Ee,m(e.title),1),s(k,{modelValue:t.data.params.search,"onUpdate:modelValue":r[0]||(r[0]=o=>t.data.params.search=o),placeholder:"Search...",onInput:t.onRefresh},null,8,["modelValue","onInput"])])]),paginatorstart:l(()=>[s(f,{icon:"pi pi-refresh",text:"",onClick:t.onRefresh},null,8,["onClick"])]),empty:l(()=>r[3]||(r[3]=[b("No data found.")])),default:l(()=>[s(c,{field:"id",header:"No",sortable:""}),s(c,{field:"player.name",header:"Player",sortable:""}),s(c,{field:"club.name",header:"Club",sortable:""}),s(c,{field:"started_at",header:"Book Day",sortable:""},{body:l(({data:o})=>[b(m(o.started_at?w(ue)(w(pe)(o.started_at)):""),1)]),_:1}),s(c,{field:"modified_at",header:"Job date",sortable:""},{body:l(({data:o})=>[u("span",{class:K({"text-red-500":w(he)(o.modified_at)})},m(o.modified_at),3)]),_:1}),s(c,{field:"timetables",header:"Timetables"},{body:l(({data:o})=>[(p(!0),h(E,null,R(o.timetablesNames,d=>(p(),g(P,{key:d.id,value:d.name},null,8,["value"]))),128))]),_:1}),s(c,{field:"status",header:"Status"},{body:l(({data:o})=>[u("span",{class:K({"text-green-700":o.status==="on-time","text-blue-700":o.status==="closed","text-red-700":o.status==="time-out"})},m(o.status),3)]),_:1}),s(c,{header:"Resources"},{body:l(({data:o})=>[s(f,{onClick:d=>{a.booking=o,v(d)},variant:"text"},{default:l(()=>r[4]||(r[4]=[b("Resources")])),_:2,__:[4]},1032,["onClick"])]),_:1}),s(c,{header:"Actions"},{body:l(({data:o})=>[t.can(["playtomic.booking_edit"])?(p(),g(f,{key:0,icon:"pi pi-pencil",outlined:"",rounded:"",class:"mr-2",onClick:d=>t.router.visit(i.route("playtomic.bookings.edit",o.id))},null,8,["onClick"])):D("",!0),t.can(["playtomic.booking_delete"])?(p(),g(f,{key:1,icon:"pi pi-trash",severity:"danger",outlined:"",rounded:"",onClick:()=>{t.deleteDialog.value=!0,a.booking=o}},null,8,["onClick"])):D("",!0)]),_:1})]),_:1},8,["value","rows","totalRecords","first","onPage","onSort"]),s(A,{visible:t.deleteDialog.value,"onUpdate:visible":r[2]||(r[2]=o=>t.deleteDialog.value=o),header:"Confirm",modal:"",style:{width:"450px"}},{footer:l(()=>[s(f,{label:"No",icon:"pi pi-times",text:"",onClick:r[1]||(r[1]=o=>t.deleteDialog.value=!1)}),s(f,{label:"Yes",icon:"pi pi-check",onClick:t.deleteData},null,8,["onClick"])]),default:l(()=>{var o,d;return[u("span",null,[r[5]||(r[5]=b("Are you sure you want to delete booking ")),u("b",null,m((d=(o=a.booking)==null?void 0:o.player)==null?void 0:d.name),1),r[6]||(r[6]=b("?"))])]}),_:1},8,["visible"]),s(I,{ref_key:"popoverResources",ref:n},{default:l(()=>{var o;return[r[7]||(r[7]=u("div",null,[u("h3",null,"Resources")],-1)),u("div",De,[(p(!0),h(E,null,R((o=a.booking)==null?void 0:o.resourcesNames,d=>(p(),h("p",{key:d.id},[u("span",null,m(d.name),1)]))),128))])]}),_:1,__:[7]},512)],64)}}};export{Me as default};
