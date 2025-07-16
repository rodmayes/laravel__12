import{i as H,B as N,y as F,U as j,s as U,ae as V,A as Z,af as M,ag as J,G as Y,ah as S,ai as q,X as G,H as L,I as X,J as Q,$ as W,p as g,b as p,w as l,d as s,M as $,m as C,k as _,g as D,c as h,K as x,r as ee,a as te,f as b,t as m,u as w,e as u,n as K,F as E,j as T}from"./app-BxDWh9sJ.js";import{O as y,C as ne}from"./index-CNIqUNf5.js";import{F as oe,s as ie}from"./index-D_Abxerg.js";import{a as re,s as se}from"./index-C7BBl_90.js";import{s as ae}from"./index-WpPmkClo.js";import{s as le}from"./index-BUNM_beL.js";import{s as de}from"./index-cr_iwp2r.js";import{d as O,t as ce,c as ue,p as pe}from"./useFormatters-DgKqr0j9.js";import"./index-CFj-I2Rk.js";import"./index-wk5GZ11B.js";import"./index-B-3qvxa5.js";import"./index-Cx0fiM5w.js";import"./index-D9-lTZn9.js";import"./index-DI3l9ENK.js";import"./index-BYnktkzF.js";import"./index-4MPmKNJR.js";import"./index-4hwA_LJY.js";import"./index-DkWp33hi.js";import"./index-DCn_lInP.js";var fe=H`
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
`,ve={root:"p-popover p-component",content:"p-popover-content"},me=N.extend({name:"popover",style:fe,classes:ve}),he={name:"BasePopover",extends:U,props:{dismissable:{type:Boolean,default:!0},appendTo:{type:[String,Object],default:"body"},baseZIndex:{type:Number,default:0},autoZIndex:{type:Boolean,default:!0},breakpoints:{type:Object,default:null},closeOnEscape:{type:Boolean,default:!0}},style:me,provide:function(){return{$pcPopover:this,$parentInstance:this}}},z={name:"Popover",extends:he,inheritAttrs:!1,emits:["show","hide"],data:function(){return{visible:!1}},watch:{dismissable:{immediate:!0,handler:function(e){e?this.bindOutsideClickListener():this.unbindOutsideClickListener()}}},selfClick:!1,target:null,eventTarget:null,outsideClickListener:null,scrollHandler:null,resizeListener:null,container:null,styleElement:null,overlayEventListener:null,documentKeydownListener:null,beforeUnmount:function(){this.dismissable&&this.unbindOutsideClickListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.destroyStyle(),this.unbindResizeListener(),this.target=null,this.container&&this.autoZIndex&&L.clear(this.container),this.overlayEventListener&&(y.off("overlay-click",this.overlayEventListener),this.overlayEventListener=null),this.container=null},mounted:function(){this.breakpoints&&this.createStyle()},methods:{toggle:function(e,o){this.visible?this.hide():this.show(e,o)},show:function(e,o){this.visible=!0,this.eventTarget=e.currentTarget,this.target=o||e.currentTarget},hide:function(){this.visible=!1},onContentClick:function(){this.selfClick=!0},onEnter:function(e){var o=this;X(e,{position:"absolute",top:"0"}),this.alignOverlay(),this.dismissable&&this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.autoZIndex&&L.set("overlay",e,this.baseZIndex+this.$primevue.config.zIndex.overlay),this.overlayEventListener=function(a){o.container.contains(a.target)&&(o.selfClick=!0)},this.focus(),y.on("overlay-click",this.overlayEventListener),this.$emit("show"),this.closeOnEscape&&this.bindDocumentKeyDownListener()},onLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.unbindDocumentKeyDownListener(),y.off("overlay-click",this.overlayEventListener),this.overlayEventListener=null,this.$emit("hide")},onAfterLeave:function(e){this.autoZIndex&&L.clear(e)},alignOverlay:function(){Y(this.container,this.target,!1);var e=S(this.container),o=S(this.target),a=0;e.left<o.left&&(a=o.left-e.left),this.container.style.setProperty(q("popover.arrow.left").name,"".concat(a,"px")),e.top<o.top&&(this.container.setAttribute("data-p-popover-flipped","true"),!this.isUnstyled&&G(this.container,"p-popover-flipped"))},onContentKeydown:function(e){e.code==="Escape"&&this.closeOnEscape&&(this.hide(),J(this.target))},onButtonKeydown:function(e){switch(e.code){case"ArrowDown":case"ArrowUp":case"ArrowLeft":case"ArrowRight":e.preventDefault()}},focus:function(){var e=this.container.querySelector("[autofocus]");e&&e.focus()},onKeyDown:function(e){e.code==="Escape"&&this.closeOnEscape&&(this.visible=!1)},bindDocumentKeyDownListener:function(){this.documentKeydownListener||(this.documentKeydownListener=this.onKeyDown.bind(this),window.document.addEventListener("keydown",this.documentKeydownListener))},unbindDocumentKeyDownListener:function(){this.documentKeydownListener&&(window.document.removeEventListener("keydown",this.documentKeydownListener),this.documentKeydownListener=null)},bindOutsideClickListener:function(){var e=this;!this.outsideClickListener&&M()&&(this.outsideClickListener=function(o){e.visible&&!e.selfClick&&!e.isTargetClicked(o)&&(e.visible=!1),e.selfClick=!1},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null,this.selfClick=!1)},bindScrollListener:function(){var e=this;this.scrollHandler||(this.scrollHandler=new ne(this.target,function(){e.visible&&(e.visible=!1)})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var e=this;this.resizeListener||(this.resizeListener=function(){e.visible&&!Z()&&(e.visible=!1)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},isTargetClicked:function(e){return this.eventTarget&&(this.eventTarget===e.target||this.eventTarget.contains(e.target))},containerRef:function(e){this.container=e},createStyle:function(){if(!this.styleElement&&!this.isUnstyled){var e;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",V(this.styleElement,"nonce",(e=this.$primevue)===null||e===void 0||(e=e.config)===null||e===void 0||(e=e.csp)===null||e===void 0?void 0:e.nonce),document.head.appendChild(this.styleElement);var o="";for(var a in this.breakpoints)o+=`
                        @media screen and (max-width: `.concat(a,`) {
                            .p-popover[`).concat(this.$attrSelector,`] {
                                width: `).concat(this.breakpoints[a],` !important;
                            }
                        }
                    `);this.styleElement.innerHTML=o}},destroyStyle:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)},onOverlayClick:function(e){y.emit("overlay-click",{originalEvent:e,target:this.target})}},directives:{focustrap:oe,ripple:j},components:{Portal:F}},be=["aria-modal"];function ye(t,e,o,a,v,i){var r=Q("Portal"),k=W("focustrap");return p(),g(r,{appendTo:t.appendTo},{default:l(function(){return[s($,C({name:"p-popover",onEnter:i.onEnter,onLeave:i.onLeave,onAfterLeave:i.onAfterLeave},t.ptm("transition")),{default:l(function(){return[v.visible?_((p(),h("div",C({key:0,ref:i.containerRef,role:"dialog","aria-modal":v.visible,onClick:e[3]||(e[3]=function(){return i.onOverlayClick&&i.onOverlayClick.apply(i,arguments)}),class:t.cx("root")},t.ptmi("root")),[t.$slots.container?x(t.$slots,"container",{key:0,closeCallback:i.hide,keydownCallback:function(d){return i.onButtonKeydown(d)}}):(p(),h("div",C({key:1,class:t.cx("content"),onClick:e[0]||(e[0]=function(){return i.onContentClick&&i.onContentClick.apply(i,arguments)}),onMousedown:e[1]||(e[1]=function(){return i.onContentClick&&i.onContentClick.apply(i,arguments)}),onKeydown:e[2]||(e[2]=function(){return i.onContentKeydown&&i.onContentKeydown.apply(i,arguments)})},t.ptm("content")),[x(t.$slots,"default")],16))],16,be)),[[k]]):D("",!0)]}),_:3},16,["onEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo"])}z.render=ye;function ge(t,...e){const o=O.bind(null,e.find(a=>typeof a=="object"));return e.map(o)}function R(t,e){const o=ce(t,e==null?void 0:e.in);return o.setHours(0,0,0,0),o}function ke(t){return O(t,Date.now())}function Le(t,e,o){const[a,v]=ge(o==null?void 0:o.in,t,e);return+R(a)==+R(v)}function Ce(t,e){return Le(O((e==null?void 0:e.in)||t,t),ke((e==null?void 0:e.in)||t))}const we={class:"flex justify-between"},Ee={class:"text-xl font-bold"},De={class:"px-3 py-2"},Je={__name:"BookingTable",props:{items:Object,onPageChange:Function,onSortChange:Function,onRefresh:Function,deleteDialog:Object,data:Object,deleteData:Function,router:Object,can:Function},setup(t){const e=t,o=ee(),a=te({booking:null}),v=i=>o.value.toggle(i);return(i,r)=>{const k=de,f=le,d=re,P=ae,B=se,A=ie,I=z;return p(),h(E,null,[s(B,{value:t.items.data,paginator:"",lazy:"",rows:t.items.per_page,totalRecords:t.items.total,first:(t.items.current_page-1)*t.items.per_page,rowsPerPageOptions:[5,10,20,50],sortMode:"multiple",onPage:t.onPageChange,onSort:t.onSortChange},{header:l(()=>[u("div",we,[u("span",Ee,m(e.title),1),s(k,{modelValue:t.data.params.search,"onUpdate:modelValue":r[0]||(r[0]=n=>t.data.params.search=n),placeholder:"Search...",onInput:r[1]||(r[1]=n=>i.emit("onRefresh"))},null,8,["modelValue"])])]),paginatorstart:l(()=>[s(f,{icon:"pi pi-refresh",text:"",onClick:r[2]||(r[2]=n=>i.emit("onRefresh"))})]),empty:l(()=>r[5]||(r[5]=[b("No data found.")])),default:l(()=>[s(d,{field:"id",header:"No",sortable:""}),s(d,{field:"player.name",header:"Player",sortable:""}),s(d,{field:"club.name",header:"Club",sortable:""}),s(d,{field:"started_at",header:"Book Day",sortable:""},{body:l(({data:n})=>[b(m(n.started_at?w(ue)(w(pe)(n.started_at)):""),1)]),_:1}),s(d,{field:"modified_at",header:"Job date",sortable:""},{body:l(({data:n})=>[u("span",{class:K({"text-red-500":w(Ce)(n.modified_at)})},m(n.modified_at),3)]),_:1}),s(d,{field:"timetables",header:"Timetables"},{body:l(({data:n})=>[(p(!0),h(E,null,T(n.timetablesNames,c=>(p(),g(P,{key:c.id,value:c.name},null,8,["value"]))),128))]),_:1}),s(d,{field:"status",header:"Status"},{body:l(({data:n})=>[u("span",{class:K({"text-green-700":n.status==="on-time","text-blue-700":n.status==="closed","text-red-700":n.status==="time-out"})},m(n.status),3)]),_:1}),s(d,{header:"Resources"},{body:l(({data:n})=>[s(f,{onClick:c=>{a.booking=n,v(c)},variant:"text"},{default:l(()=>r[6]||(r[6]=[b("Resources")])),_:2,__:[6]},1032,["onClick"])]),_:1}),s(d,{header:"Actions"},{body:l(({data:n})=>[t.can(["playtomic.booking_edit"])?(p(),g(f,{key:0,icon:"pi pi-pencil",outlined:"",rounded:"",class:"mr-2",onClick:c=>t.router.visit(i.route("playtomic.bookings.edit",n.id))},null,8,["onClick"])):D("",!0),t.can(["playtomic.booking_delete"])?(p(),g(f,{key:1,icon:"pi pi-trash",severity:"danger",outlined:"",rounded:"",onClick:()=>{t.deleteDialog.value=!0,a.booking=n}},null,8,["onClick"])):D("",!0)]),_:1})]),_:1},8,["value","rows","totalRecords","first","onPage","onSort"]),s(A,{visible:t.deleteDialog.value,"onUpdate:visible":r[4]||(r[4]=n=>t.deleteDialog.value=n),header:"Confirm",modal:"",style:{width:"450px"}},{footer:l(()=>[s(f,{label:"No",icon:"pi pi-times",text:"",onClick:r[3]||(r[3]=n=>t.deleteDialog.value=!1)}),s(f,{label:"Yes",icon:"pi pi-check",onClick:t.deleteData},null,8,["onClick"])]),default:l(()=>{var n,c;return[u("span",null,[r[7]||(r[7]=b("Are you sure you want to delete booking ")),u("b",null,m((c=(n=a.booking)==null?void 0:n.player)==null?void 0:c.name),1),r[8]||(r[8]=b("?"))])]}),_:1},8,["visible"]),s(I,{ref_key:"popoverResources",ref:o},{default:l(()=>{var n;return[r[9]||(r[9]=u("div",null,[u("h3",null,"Resources")],-1)),u("div",De,[(p(!0),h(E,null,T((n=a.booking)==null?void 0:n.resourcesNames,c=>(p(),h("p",{key:c.id},[u("span",null,m(c.name),1)]))),128))])]}),_:1,__:[9]},512)],64)}}};export{Je as default};
