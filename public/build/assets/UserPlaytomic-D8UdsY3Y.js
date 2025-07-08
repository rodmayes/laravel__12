import{s as T}from"./index-BU8twPeb.js";import{h as D,B as x,E as k,f as g,o as d,y as I,m as G,T as E,F as v,N as F,c as N,w as u,b as t,a as r,g as $,u as i,t as m}from"./app-BtWIXAZa.js";import{s as U}from"./index-L9jds0UM.js";import{s as j}from"./index-X78qv4tB.js";import"./index-D1785TMw.js";var R=D`
    .p-inputgroup,
    .p-inputgroup .p-iconfield,
    .p-inputgroup .p-floatlabel,
    .p-inputgroup .p-iftalabel {
        display: flex;
        align-items: stretch;
        width: 100%;
    }

    .p-inputgroup .p-inputtext,
    .p-inputgroup .p-inputwrapper {
        flex: 1 1 auto;
        width: 1%;
    }

    .p-inputgroupaddon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: dt('inputgroup.addon.padding');
        background: dt('inputgroup.addon.background');
        color: dt('inputgroup.addon.color');
        border-block-start: 1px solid dt('inputgroup.addon.border.color');
        border-block-end: 1px solid dt('inputgroup.addon.border.color');
        min-width: dt('inputgroup.addon.min.width');
    }

    .p-inputgroupaddon:first-child,
    .p-inputgroupaddon + .p-inputgroupaddon {
        border-inline-start: 1px solid dt('inputgroup.addon.border.color');
    }

    .p-inputgroupaddon:last-child {
        border-inline-end: 1px solid dt('inputgroup.addon.border.color');
    }

    .p-inputgroupaddon:has(.p-button) {
        padding: 0;
        overflow: hidden;
    }

    .p-inputgroupaddon .p-button {
        border-radius: 0;
    }

    .p-inputgroup > .p-component,
    .p-inputgroup > .p-inputwrapper > .p-component,
    .p-inputgroup > .p-iconfield > .p-component,
    .p-inputgroup > .p-floatlabel > .p-component,
    .p-inputgroup > .p-floatlabel > .p-inputwrapper > .p-component,
    .p-inputgroup > .p-iftalabel > .p-component,
    .p-inputgroup > .p-iftalabel > .p-inputwrapper > .p-component {
        border-radius: 0;
        margin: 0;
    }

    .p-inputgroupaddon:first-child,
    .p-inputgroup > .p-component:first-child,
    .p-inputgroup > .p-inputwrapper:first-child > .p-component,
    .p-inputgroup > .p-iconfield:first-child > .p-component,
    .p-inputgroup > .p-floatlabel:first-child > .p-component,
    .p-inputgroup > .p-floatlabel:first-child > .p-inputwrapper > .p-component,
    .p-inputgroup > .p-iftalabel:first-child > .p-component,
    .p-inputgroup > .p-iftalabel:first-child > .p-inputwrapper > .p-component {
        border-start-start-radius: dt('inputgroup.addon.border.radius');
        border-end-start-radius: dt('inputgroup.addon.border.radius');
    }

    .p-inputgroupaddon:last-child,
    .p-inputgroup > .p-component:last-child,
    .p-inputgroup > .p-inputwrapper:last-child > .p-component,
    .p-inputgroup > .p-iconfield:last-child > .p-component,
    .p-inputgroup > .p-floatlabel:last-child > .p-component,
    .p-inputgroup > .p-floatlabel:last-child > .p-inputwrapper > .p-component,
    .p-inputgroup > .p-iftalabel:last-child > .p-component,
    .p-inputgroup > .p-iftalabel:last-child > .p-inputwrapper > .p-component {
        border-start-end-radius: dt('inputgroup.addon.border.radius');
        border-end-end-radius: dt('inputgroup.addon.border.radius');
    }

    .p-inputgroup .p-component:focus,
    .p-inputgroup .p-component.p-focus,
    .p-inputgroup .p-inputwrapper-focus,
    .p-inputgroup .p-component:focus ~ label,
    .p-inputgroup .p-component.p-focus ~ label,
    .p-inputgroup .p-inputwrapper-focus ~ label {
        z-index: 1;
    }

    .p-inputgroup > .p-button:not(.p-button-icon-only) {
        width: auto;
    }

    .p-inputgroup .p-iconfield + .p-iconfield .p-inputtext {
        border-inline-start: 0;
    }
`,z={root:"p-inputgroup"},O=x.extend({name:"inputgroup",style:R,classes:z}),q={name:"BaseInputGroup",extends:k,style:O,provide:function(){return{$pcInputGroup:this,$parentInstance:this}}},B={name:"InputGroup",extends:q,inheritAttrs:!1};function H(n,f,e,s,p,l){return d(),g("div",G({class:n.cx("root")},n.ptmi("root")),[I(n.$slots,"default")],16)}B.render=H;var J={root:"p-inputgroupaddon"},K=x.extend({name:"inputgroupaddon",classes:J}),L={name:"BaseInputGroupAddon",extends:k,style:K,provide:function(){return{$pcInputGroupAddon:this,$parentInstance:this}}},S={name:"InputGroupAddon",extends:L,inheritAttrs:!1};function M(n,f,e,s,p,l){return d(),g("div",G({class:n.cx("root")},n.ptmi("root")),[I(n.$slots,"default")],16)}S.render=M;const Q={class:"flex flex-col gap-4"},W={key:0,class:"text-red-500"},X={class:"p-inputgroup"},Y={key:0,class:"text-red-500"},Z={class:"flex justify-between items-center gap-2 mt-4"},pp={class:"border-t pt-4 mt-4"},op={class:"p-2 bg-gray-100 rounded"},tp={class:"mt-2"},ep={class:"p-2 bg-gray-100 rounded"},up={__name:"UserPlaytomic",props:{show:Boolean,user:Object},emits:["close","updated"],setup(n,{emit:f}){const e=n,s=f,p=E({playtomic_id:"",playtomic_password:""}),l=v(!1),b=v(!1),y=v(!1);F(()=>{e.show&&e.user&&(p.reset(),p.playtomic_id=e.user.playtomic_id||"")});const P=()=>{l.value=!0,p.put(route("playtomic.user.update",e.user.id),{preserveScroll:!0,onSuccess:()=>s("updated"),onFinish:()=>l.value=!1})},V=()=>{b.value=!0,p.put(route("playtomic.user.save-password",e.user.id),{preserveScroll:!0,onSuccess:()=>{s("updated"),p.playtomic_password=""},onFinish:()=>b.value=!1})},A=()=>{y.value=!0,p.get(route("playtomic.user.refresh-token",e.user.id),{onSuccess:()=>s("updated"),onFinish:()=>y.value=!1})};return(np,o)=>{const h=j,c=U,w=S,_=B,C=T;return d(),N(C,{visible:e.show,"onUpdate:visible":o[3]||(o[3]=a=>e.show=a),header:"Editar Playtomic",modal:"",style:{width:"40rem"},closable:!1},{default:u(()=>[t("div",Q,[t("div",null,[o[4]||(o[4]=t("label",{for:"playtomic_id",class:"block mb-1"},"Playtomic ID",-1)),r(_,null,{default:u(()=>[r(h,{id:"playtomic_id",type:"text",modelValue:i(p).playtomic_id,"onUpdate:modelValue":o[0]||(o[0]=a=>i(p).playtomic_id=a),class:"w-full"},null,8,["modelValue"]),r(w,null,{default:u(()=>[r(c,{icon:"pi pi-save",severity:"secondary",onClick:P,loading:l.value,title:"Guardar ID"},null,8,["loading"])]),_:1})]),_:1}),i(p).errors.playtomic_id?(d(),g("small",W,m(i(p).errors.playtomic_id),1)):$("",!0)]),t("div",null,[o[5]||(o[5]=t("label",{for:"playtomic_password",class:"block mb-1"},"Playtomic Password",-1)),t("div",X,[r(_,null,{default:u(()=>[r(h,{id:"playtomic_password",type:"password",modelValue:i(p).playtomic_password,"onUpdate:modelValue":o[1]||(o[1]=a=>i(p).playtomic_password=a),class:"w-full",autocomplete:"new-password"},null,8,["modelValue"]),r(w,null,{default:u(()=>[r(c,{icon:"pi pi-save",severity:"secondary",onClick:V,loading:b.value},null,8,["loading"])]),_:1})]),_:1})]),i(p).errors.playtomic_password?(d(),g("small",Y,m(i(p).errors.playtomic_password),1)):$("",!0)]),t("div",Z,[r(c,{label:"Refresh Playtomic Token",icon:"pi pi-refresh",onClick:A,loading:y.value},null,8,["loading"]),r(c,{label:"Cerrar",severity:"secondary",onClick:o[2]||(o[2]=a=>s("close"))})]),t("div",pp,[t("div",null,[o[6]||(o[6]=t("strong",null,"Token:",-1)),t("pre",op,m(e.user.playtomic_token||"-"),1)]),t("div",tp,[o[7]||(o[7]=t("strong",null,"Refresh Token:",-1)),t("pre",ep,m(e.user.playtomic_refresh_token||"-"),1)])])])]),_:1},8,["visible"])}}};export{up as default};
