import{s as H}from"./index-Cg18yx5v.js";import{s as J}from"./index-BLljuaDF.js";import{h as z,B as I,E as V,f as n,o,g as f,b as r,m as d,y as b,F as S,G as K,a as l,w as a,H as C,I as N,J as y,K as h,e as $,t as M,c as B,u as Y,L as P}from"./app-BUGNN5JA.js";import{s as q}from"./index-Cwf3xXSf.js";import{s as Q}from"./index-UnlfduWp.js";import{_ as R}from"./AppLayout-hxMZJ4a3.js";import{l as D}from"./loadToast-CkDWCmsi.js";import"./index-U_6rZySG.js";import"./index-BKQe30te.js";import"./index-E7fOk3yg.js";import"./index-DMS4686z.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-CH1mWxBr.js";import"./index-BO7XlLjM.js";import"./index-BH19Py2R.js";import"./index-Dfx5MwNy.js";var X=z`
    .p-card {
        background: dt('card.background');
        color: dt('card.color');
        box-shadow: dt('card.shadow');
        border-radius: dt('card.border.radius');
        display: flex;
        flex-direction: column;
    }

    .p-card-caption {
        display: flex;
        flex-direction: column;
        gap: dt('card.caption.gap');
    }

    .p-card-body {
        padding: dt('card.body.padding');
        display: flex;
        flex-direction: column;
        gap: dt('card.body.gap');
    }

    .p-card-title {
        font-size: dt('card.title.font.size');
        font-weight: dt('card.title.font.weight');
    }

    .p-card-subtitle {
        color: dt('card.subtitle.color');
    }
`,Z={root:"p-card p-component",header:"p-card-header",body:"p-card-body",caption:"p-card-caption",title:"p-card-title",subtitle:"p-card-subtitle",content:"p-card-content",footer:"p-card-footer"},_=I.extend({name:"card",style:X,classes:Z}),ee={name:"BaseCard",extends:V,style:_,provide:function(){return{$pcCard:this,$parentInstance:this}}},A={name:"Card",extends:ee,inheritAttrs:!1};function se(e,c,v,u,t,g){return o(),n("div",d({class:e.cx("root")},e.ptmi("root")),[e.$slots.header?(o(),n("div",d({key:0,class:e.cx("header")},e.ptm("header")),[b(e.$slots,"header")],16)):f("",!0),r("div",d({class:e.cx("body")},e.ptm("body")),[e.$slots.title||e.$slots.subtitle?(o(),n("div",d({key:0,class:e.cx("caption")},e.ptm("caption")),[e.$slots.title?(o(),n("div",d({key:0,class:e.cx("title")},e.ptm("title")),[b(e.$slots,"title")],16)):f("",!0),e.$slots.subtitle?(o(),n("div",d({key:1,class:e.cx("subtitle")},e.ptm("subtitle")),[b(e.$slots,"subtitle")],16)):f("",!0)],16)):f("",!0),r("div",d({class:e.cx("content")},e.ptm("content")),[b(e.$slots,"content")],16),e.$slots.footer?(o(),n("div",d({key:1,class:e.cx("footer")},e.ptm("footer")),[b(e.$slots,"footer")],16)):f("",!0)],16)],16)}A.render=se;var te=z`
    .p-progressspinner {
        position: relative;
        margin: 0 auto;
        width: 100px;
        height: 100px;
        display: inline-block;
    }

    .p-progressspinner::before {
        content: '';
        display: block;
        padding-top: 100%;
    }

    .p-progressspinner-spin {
        height: 100%;
        transform-origin: center center;
        width: 100%;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
        animation: p-progressspinner-rotate 2s linear infinite;
    }

    .p-progressspinner-circle {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: 0;
        stroke: dt('progressspinner.colorOne');
        animation:
            p-progressspinner-dash 1.5s ease-in-out infinite,
            p-progressspinner-color 6s ease-in-out infinite;
        stroke-linecap: round;
    }

    @keyframes p-progressspinner-rotate {
        100% {
            transform: rotate(360deg);
        }
    }
    @keyframes p-progressspinner-dash {
        0% {
            stroke-dasharray: 1, 200;
            stroke-dashoffset: 0;
        }
        50% {
            stroke-dasharray: 89, 200;
            stroke-dashoffset: -35px;
        }
        100% {
            stroke-dasharray: 89, 200;
            stroke-dashoffset: -124px;
        }
    }
    @keyframes p-progressspinner-color {
        100%,
        0% {
            stroke: dt('progressspinner.color.one');
        }
        40% {
            stroke: dt('progressspinner.color.two');
        }
        66% {
            stroke: dt('progressspinner.color.three');
        }
        80%,
        90% {
            stroke: dt('progressspinner.color.four');
        }
    }
`,re={root:"p-progressspinner",spin:"p-progressspinner-spin",circle:"p-progressspinner-circle"},oe=I.extend({name:"progressspinner",style:te,classes:re}),ie={name:"BaseProgressSpinner",extends:V,props:{strokeWidth:{type:String,default:"2"},fill:{type:String,default:"none"},animationDuration:{type:String,default:"2s"}},style:oe,provide:function(){return{$pcProgressSpinner:this,$parentInstance:this}}},E={name:"ProgressSpinner",extends:ie,inheritAttrs:!1,computed:{svgStyle:function(){return{"animation-duration":this.animationDuration}}}},ae=["fill","stroke-width"];function ne(e,c,v,u,t,g){return o(),n("div",d({class:e.cx("root"),role:"progressbar"},e.ptmi("root")),[(o(),n("svg",d({class:e.cx("spin"),viewBox:"25 25 50 50",style:g.svgStyle},e.ptm("spin")),[r("circle",d({class:e.cx("circle"),cx:"50",cy:"50",r:"20",fill:e.fill,"stroke-width":e.strokeWidth,strokeMiterlimit:"10"},e.ptm("circle")),null,16,ae)],16))],16)}E.render=ne;const le={class:"card"},de={class:"flex flex-col gap-2"},pe={class:"flex flex-wrap gap-2 max-w-full overflow-x-auto"},ce={class:"grid grid-cols-1 md:grid-cols-4 gap-4 my-4"},me={class:"flex flex-wrap gap-2"},ue={class:"flex justify-center items-center py-6"},De={__name:"Index",props:{title:String},setup(e){const c=S(!1),v=e;D();const{toast:u}=D(),t=K({sendMail:!1,makeNumbersConfirm:!1,excludedNumbers:[],combinations:[]}),g=()=>{t.makeNumbersConfirm=!1,c.value=!0,P.post(route("lottery.combinations.make-magik-numbers"),{excludedNumbers:t.excludedNumbers}).then(p=>{t.combinations=p.data.combinations,u.add({severity:"success",summary:"Magik numbers",detail:"Magik numbers generated successfully",life:3e3})}).catch(()=>{u.add({severity:"error",summary:"Magik numbers",detail:"Magik numbers generated unSuccessfully",life:3e3})}).finally(()=>{c.value=!1})},L=()=>{t.sendMail=!1,P.post(route("lottery.combinations.send-mail-with-combinations"),{combinations:t.combinations}).then(()=>{u.add({severity:"success",summary:"Mail",detail:"Mail send successfully",life:3e3}),t.sendMail=!1}).catch(()=>{u.add({severity:"error",summary:"Mail",detail:"Magik send unSuccessfully",life:3e3})})},T=p=>{const s=t.excludedNumbers.indexOf(p);s===-1?t.excludedNumbers.push(p):t.excludedNumbers.splice(s,1)},j=()=>t.combinations.map(p=>[...p].sort((s,m)=>s-m)),F=S([{label:"Lottery"},{label:v.title,url:route("lottery.combinations.index")}]);return(p,s)=>{const m=Q,G=q,x=A,O=J,w=H;return o(),n(y,null,[l(R,{items:F.value},{default:a(()=>[r("div",le,[l(G,{class:"mb-4"},{start:a(()=>[C(l(m,{label:"Make magik numbers",onClick:s[0]||(s[0]=i=>t.makeNumbersConfirm=!0),icon:"pi pi-dollar",severity:"success",size:"small",class:"mr-2",disabled:c.value},null,8,["disabled"]),[[N,p.can(["lottery.magik_numbers_create"])]]),C(l(m,{label:"Send mail with combinations",onClick:L,icon:"pi pi-envelope",severity:"warn",size:"small",class:"mr-2"},null,512),[[N,t.combinations.length>0]])]),_:1}),l(x,null,{title:a(()=>s[4]||(s[4]=[$("Exclude numbers")])),content:a(()=>[s[5]||(s[5]=r("p",{class:"m-0"}," Set excluded number for combinations ",-1)),r("div",de,[r("div",pe,[(o(),n(y,null,h(49,i=>l(m,{key:"complementary-"+i,onClick:k=>T(i),severity:t.excludedNumbers.includes(i)?"":"secondary",class:"w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center border-gray-300 dark:border-gray-600"},{default:a(()=>[$(M(i),1)]),_:2},1032,["onClick","severity"])),64))])])]),_:1}),r("div",ce,[(o(!0),n(y,null,h(j(),(i,k)=>(o(),B(x,{key:"comb-"+k},{title:a(()=>[$("Combination "+M(k+1),1)]),content:a(()=>[r("div",me,[(o(!0),n(y,null,h(i,(U,W)=>(o(),B(O,{key:W,value:U,severity:"secondary"},null,8,["value"]))),128))])]),_:2},1024))),128))])])]),_:1},8,["items"]),l(w,{visible:c.value,"onUpdate:visible":s[1]||(s[1]=i=>c.value=i),modal:"",closable:!1,style:{width:"300px"}},{header:a(()=>s[6]||(s[6]=[r("span",{class:"font-bold"},"Generating combinations",-1)])),footer:a(()=>s[7]||(s[7]=[r("span",{class:"text-sm text-gray-500"},"Please wait...",-1)])),default:a(()=>[r("div",ue,[l(Y(E))])]),_:1},8,["visible"]),l(w,{visible:t.makeNumbersConfirm,"onUpdate:visible":s[3]||(s[3]=i=>t.makeNumbersConfirm=i),style:{width:"450px"},header:"Confirm",modal:!0},{footer:a(()=>[l(m,{label:"No",icon:"pi pi-times",text:"",onClick:s[2]||(s[2]=i=>t.makeNumbersConfirm=!1)}),l(m,{label:"Yes",icon:"pi pi-check",onClick:g})]),default:a(()=>[s[8]||(s[8]=r("div",{class:"flex items-center gap-4"},[r("i",{class:"pi pi-exclamation-triangle !text-3xl"}),r("span",null," Are you sure you want to generate magik numbers? ")],-1))]),_:1,__:[8]},8,["visible"])],64)}}};export{De as default};
