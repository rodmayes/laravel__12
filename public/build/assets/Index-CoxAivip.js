import{s as J}from"./index-C4lvvraB.js";import{s as K}from"./index-C7q-yZ8e.js";import{k as z,B as V,J as j,c as n,b as o,g as f,e as r,m as p,D as b,r as N,a as Y,h,d,w as i,K as M,L as B,F as y,M as $,f as x,t as D,i as I,u as q}from"./app-BitM9Esh.js";import{s as Q}from"./index-CTi-Lker.js";import{s as R}from"./index-ChyoiKwM.js";import{_ as X}from"./AppLayout-CLNBn6lV.js";import{l as P}from"./loadToast-DRzFDdxN.js";import"./index-CxtCzB2E.js";import"./index-CyWtV5jm.js";import"./index-dCAdXrLa.js";import"./DropdownLink-CjHD7zJG.js";import"./index-BRhRa0mV.js";import"./index-BMoRl1xm.js";import"./index-BoR1qmRO.js";var Z=z`
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
`,_={root:"p-card p-component",header:"p-card-header",body:"p-card-body",caption:"p-card-caption",title:"p-card-title",subtitle:"p-card-subtitle",content:"p-card-content",footer:"p-card-footer"},ee=V.extend({name:"card",style:Z,classes:_}),se={name:"BaseCard",extends:j,style:ee,provide:function(){return{$pcCard:this,$parentInstance:this}}},A={name:"Card",extends:se,inheritAttrs:!1};function te(e,c,v,u,t,g){return o(),n("div",p({class:e.cx("root")},e.ptmi("root")),[e.$slots.header?(o(),n("div",p({key:0,class:e.cx("header")},e.ptm("header")),[b(e.$slots,"header")],16)):f("",!0),r("div",p({class:e.cx("body")},e.ptm("body")),[e.$slots.title||e.$slots.subtitle?(o(),n("div",p({key:0,class:e.cx("caption")},e.ptm("caption")),[e.$slots.title?(o(),n("div",p({key:0,class:e.cx("title")},e.ptm("title")),[b(e.$slots,"title")],16)):f("",!0),e.$slots.subtitle?(o(),n("div",p({key:1,class:e.cx("subtitle")},e.ptm("subtitle")),[b(e.$slots,"subtitle")],16)):f("",!0)],16)):f("",!0),r("div",p({class:e.cx("content")},e.ptm("content")),[b(e.$slots,"content")],16),e.$slots.footer?(o(),n("div",p({key:1,class:e.cx("footer")},e.ptm("footer")),[b(e.$slots,"footer")],16)):f("",!0)],16)],16)}A.render=te;var re=z`
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
`,oe={root:"p-progressspinner",spin:"p-progressspinner-spin",circle:"p-progressspinner-circle"},ae=V.extend({name:"progressspinner",style:re,classes:oe}),ie={name:"BaseProgressSpinner",extends:j,props:{strokeWidth:{type:String,default:"2"},fill:{type:String,default:"none"},animationDuration:{type:String,default:"2s"}},style:ae,provide:function(){return{$pcProgressSpinner:this,$parentInstance:this}}},F={name:"ProgressSpinner",extends:ie,inheritAttrs:!1,computed:{svgStyle:function(){return{"animation-duration":this.animationDuration}}}},ne=["fill","stroke-width"];function le(e,c,v,u,t,g){return o(),n("div",p({class:e.cx("root"),role:"progressbar"},e.ptmi("root")),[(o(),n("svg",p({class:e.cx("spin"),viewBox:"25 25 50 50",style:g.svgStyle},e.ptm("spin")),[r("circle",p({class:e.cx("circle"),cx:"50",cy:"50",r:"20",fill:e.fill,"stroke-width":e.strokeWidth,strokeMiterlimit:"10"},e.ptm("circle")),null,16,ne)],16))],16)}F.render=le;const de={class:"card"},pe={class:"flex flex-col gap-2"},ce={class:"flex flex-wrap gap-2 max-w-full overflow-x-auto"},me={class:"grid grid-cols-1 md:grid-cols-4 gap-4 my-4"},ue={class:"flex flex-wrap gap-2"},fe={class:"flex justify-center items-center py-6"},De={__name:"Index",props:{title:String},setup(e){const c=N(!1),v=e;P();const{toast:u}=P(),t=Y({sendMail:!1,makeNumbersConfirm:!1,excludedNumbers:[],combinations:[]}),g=()=>{t.makeNumbersConfirm=!1,c.value=!0,h.post(route("lottery.combinations.make-magik-numbers"),{excludedNumbers:t.excludedNumbers}).then(l=>{t.combinations=l.data.combinations,u.add({severity:"success",summary:"Magik numbers",detail:"Magik numbers generated successfully",life:3e3})}).catch(()=>{u.add({severity:"error",summary:"Magik numbers",detail:"Magik numbers generated unSuccessfully",life:3e3})}).finally(()=>{c.value=!1})},w=setInterval(async()=>{const l=await h.get(`/job/status/${uuid}`);l.data.status==="finished"?(status.value="Finalizado",clearInterval(w)):l.data.status==="failed"&&(status.value="Ha fallado",clearInterval(w))},3e3),L=()=>{t.sendMail=!1,h.post(route("lottery.combinations.send-mail-with-combinations"),{combinations:t.combinations}).then(()=>{u.add({severity:"success",summary:"Mail",detail:"Mail send successfully",life:3e3}),t.sendMail=!1}).catch(()=>{u.add({severity:"error",summary:"Mail",detail:"Magik send unSuccessfully",life:3e3})})},T=l=>{const s=t.excludedNumbers.indexOf(l);s===-1?t.excludedNumbers.push(l):t.excludedNumbers.splice(s,1)},E=()=>t.combinations.map(l=>[...l].sort((s,m)=>s-m)),O=N([{label:"Lottery"},{label:v.title,url:route("lottery.combinations.index")}]);return(l,s)=>{const m=R,U=Q,S=A,W=K,C=J;return o(),n(y,null,[d(X,{items:O.value},{default:i(()=>[r("div",de,[d(U,{class:"mb-4"},{start:i(()=>[M(d(m,{label:"Make magik numbers",onClick:s[0]||(s[0]=a=>t.makeNumbersConfirm=!0),icon:"pi pi-dollar",severity:"success",size:"small",class:"mr-2",disabled:c.value},null,8,["disabled"]),[[B,l.can(["lottery.magik_numbers_create"])]]),M(d(m,{label:"Send mail with combinations",onClick:L,icon:"pi pi-envelope",severity:"warn",size:"small",class:"mr-2"},null,512),[[B,t.combinations.length>0]])]),_:1}),d(S,null,{title:i(()=>s[4]||(s[4]=[x("Exclude numbers")])),content:i(()=>[s[5]||(s[5]=r("p",{class:"m-0"}," Set excluded number for combinations ",-1)),r("div",pe,[r("div",ce,[(o(),n(y,null,$(49,a=>d(m,{key:"complementary-"+a,onClick:k=>T(a),severity:t.excludedNumbers.includes(a)?"":"secondary",class:"w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center border-gray-300 dark:border-gray-600"},{default:i(()=>[x(D(a),1)]),_:2},1032,["onClick","severity"])),64))])])]),_:1}),r("div",me,[(o(!0),n(y,null,$(E(),(a,k)=>(o(),I(S,{key:"comb-"+k},{title:i(()=>[x("Combination "+D(k+1),1)]),content:i(()=>[r("div",ue,[(o(!0),n(y,null,$(a,(G,H)=>(o(),I(W,{key:H,value:G,severity:"secondary"},null,8,["value"]))),128))])]),_:2},1024))),128))])])]),_:1},8,["items"]),d(C,{visible:c.value,"onUpdate:visible":s[1]||(s[1]=a=>c.value=a),modal:"",closable:!1,style:{width:"300px"}},{header:i(()=>s[6]||(s[6]=[r("span",{class:"font-bold"},"Generating combinations",-1)])),footer:i(()=>s[7]||(s[7]=[r("span",{class:"text-sm text-gray-500"},"Please wait...",-1)])),default:i(()=>[r("div",fe,[d(q(F))])]),_:1},8,["visible"]),d(C,{visible:t.makeNumbersConfirm,"onUpdate:visible":s[3]||(s[3]=a=>t.makeNumbersConfirm=a),style:{width:"450px"},header:"Confirm",modal:!0},{footer:i(()=>[d(m,{label:"No",icon:"pi pi-times",text:"",onClick:s[2]||(s[2]=a=>t.makeNumbersConfirm=!1)}),d(m,{label:"Yes",icon:"pi pi-check",onClick:g})]),default:i(()=>[s[8]||(s[8]=r("div",{class:"flex items-center gap-4"},[r("i",{class:"pi pi-exclamation-triangle !text-3xl"}),r("span",null," Are you sure you want to generate magik numbers? ")],-1))]),_:1,__:[8]},8,["visible"])],64)}}};export{De as default};
