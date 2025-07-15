import{s as K}from"./index-C7JHgchM.js";import{s as R}from"./index-CfMFk64A.js";import{k as z,B as E,J as U,c as n,b as o,g as y,e as r,m as d,D as g,r as M,a as Y,d as l,w as a,K as B,L as T,F as h,M as C,f as S,t as I,i as D,u as q,h as N}from"./app-BlNzP-Qy.js";import{s as H}from"./index-qwsCAThh.js";import{s as Q}from"./index-CVEuAQA3.js";import{_ as X}from"./AppLayout-qSeyv-th.js";import{l as P}from"./loadToast-BsY7vkKY.js";import"./index-C5W7pBj7.js";import"./index-BYafp3sT.js";import"./index-4RaZttnM.js";import"./DropdownLink-DZ1swxXI.js";import"./index-Cf7dxWOV.js";import"./index-BXR1ZY2p.js";import"./index-CrlDN87a.js";var Z=z`
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
`,_={root:"p-card p-component",header:"p-card-header",body:"p-card-body",caption:"p-card-caption",title:"p-card-title",subtitle:"p-card-subtitle",content:"p-card-content",footer:"p-card-footer"},ee=E.extend({name:"card",style:Z,classes:_}),se={name:"BaseCard",extends:U,style:ee,provide:function(){return{$pcCard:this,$parentInstance:this}}},V={name:"Card",extends:se,inheritAttrs:!1};function te(e,m,$,c,t,v){return o(),n("div",d({class:e.cx("root")},e.ptmi("root")),[e.$slots.header?(o(),n("div",d({key:0,class:e.cx("header")},e.ptm("header")),[g(e.$slots,"header")],16)):y("",!0),r("div",d({class:e.cx("body")},e.ptm("body")),[e.$slots.title||e.$slots.subtitle?(o(),n("div",d({key:0,class:e.cx("caption")},e.ptm("caption")),[e.$slots.title?(o(),n("div",d({key:0,class:e.cx("title")},e.ptm("title")),[g(e.$slots,"title")],16)):y("",!0),e.$slots.subtitle?(o(),n("div",d({key:1,class:e.cx("subtitle")},e.ptm("subtitle")),[g(e.$slots,"subtitle")],16)):y("",!0)],16)):y("",!0),r("div",d({class:e.cx("content")},e.ptm("content")),[g(e.$slots,"content")],16),e.$slots.footer?(o(),n("div",d({key:1,class:e.cx("footer")},e.ptm("footer")),[g(e.$slots,"footer")],16)):y("",!0)],16)],16)}V.render=te;var re=z`
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
`,oe={root:"p-progressspinner",spin:"p-progressspinner-spin",circle:"p-progressspinner-circle"},ie=E.extend({name:"progressspinner",style:re,classes:oe}),ae={name:"BaseProgressSpinner",extends:U,props:{strokeWidth:{type:String,default:"2"},fill:{type:String,default:"none"},animationDuration:{type:String,default:"2s"}},style:ie,provide:function(){return{$pcProgressSpinner:this,$parentInstance:this}}},j={name:"ProgressSpinner",extends:ae,inheritAttrs:!1,computed:{svgStyle:function(){return{"animation-duration":this.animationDuration}}}},ne=["fill","stroke-width"];function le(e,m,$,c,t,v){return o(),n("div",d({class:e.cx("root"),role:"progressbar"},e.ptmi("root")),[(o(),n("svg",d({class:e.cx("spin"),viewBox:"25 25 50 50",style:v.svgStyle},e.ptm("spin")),[r("circle",d({class:e.cx("circle"),cx:"50",cy:"50",r:"20",fill:e.fill,"stroke-width":e.strokeWidth,strokeMiterlimit:"10"},e.ptm("circle")),null,16,ne)],16))],16)}j.render=le;const de={class:"card"},ce={class:"flex flex-col gap-2"},pe={class:"flex flex-wrap gap-2 max-w-full overflow-x-auto"},me={class:"grid grid-cols-1 md:grid-cols-4 gap-4 my-4"},ue={class:"flex flex-wrap gap-2"},fe={class:"flex justify-center items-center py-6"},Te={__name:"Index",props:{title:String},setup(e){const m=M(!1),$=e;P();const{toast:c}=P(),t=Y({sendMail:!1,makeNumbersConfirm:!1,excludedNumbers:[],combinations:[]}),v=()=>{t.makeNumbersConfirm=!1,m.value=!0,N.post(route("lottery.combinations.make-magik-numbers"),{excludedNumbers:t.excludedNumbers}).then(p=>{const s=p.data.uuid;s?J(s):c.add({severity:"error",summary:"Error",detail:"UUID no recibido del backend",life:3e3}),c.add({severity:"success",summary:"Magik numbers",detail:"Magik numbers in process",life:3e3})}).catch(()=>{c.add({severity:"error",summary:"Magik numbers",detail:"Magik numbers generated unSuccessfully",life:3e3})}).finally(()=>{})},A=()=>{t.sendMail=!1,N.post(route("lottery.combinations.send-mail-with-combinations"),{combinations:t.combinations}).then(()=>{c.add({severity:"success",summary:"Mail",detail:"Mail send successfully",life:3e3}),t.sendMail=!1}).catch(()=>{c.add({severity:"error",summary:"Mail",detail:"Magik send unSuccessfully",life:3e3})})},F=p=>{const s=t.excludedNumbers.indexOf(p);s===-1?t.excludedNumbers.push(p):t.excludedNumbers.splice(s,1)},L=()=>t.combinations.map(p=>[...p].sort((s,u)=>s-u)),J=p=>{let k=0;const f=setInterval(async()=>{k+=3e3;try{const b=(await N.get(route("lottery.combinations.magic-numbers-from-cache",{uuid:p}))).data;if(b&&b.length){clearInterval(f),t.combinations=b,m.value=!1,c.add({severity:"success",summary:"¡Combinations ready!",detail:"Magik numbers generated successfully",life:3e3});return}k>=3e4&&(clearInterval(f),m.value=!1,c.add({severity:"error",summary:"Timeout",detail:"Could not fetch combinations in time",life:3e3}))}catch{clearInterval(f),m.value=!1,c.add({severity:"error",summary:"Error",detail:"Failed to get job result",life:3e3})}},3e3)},O=M([{label:"Lottery"},{label:$.title,url:route("lottery.combinations.index")}]);return(p,s)=>{const u=Q,k=H,f=V,x=R,b=K;return o(),n(h,null,[l(X,{items:O.value},{default:a(()=>[r("div",de,[l(k,{class:"mb-4"},{start:a(()=>[B(l(u,{label:"Make magik numbers",onClick:s[0]||(s[0]=i=>t.makeNumbersConfirm=!0),icon:"pi pi-dollar",severity:"success",size:"small",class:"mr-2",disabled:m.value},null,8,["disabled"]),[[T,p.can(["lottery.magik_numbers_create"])]]),B(l(u,{label:"Send mail with combinations",onClick:A,icon:"pi pi-envelope",severity:"warn",size:"small",class:"mr-2"},null,512),[[T,t.combinations.length>0]])]),_:1}),l(f,null,{title:a(()=>s[4]||(s[4]=[S("Exclude numbers")])),content:a(()=>[s[5]||(s[5]=r("p",{class:"m-0"}," Set excluded number for combinations ",-1)),r("div",ce,[r("div",pe,[(o(),n(h,null,C(49,i=>l(u,{key:"complementary-"+i,onClick:w=>F(i),severity:t.excludedNumbers.includes(i)?"":"secondary",class:"w-10 h-10 text-sm font-semibold rounded-md border transition-all flex items-center justify-center border-gray-300 dark:border-gray-600"},{default:a(()=>[S(I(i),1)]),_:2},1032,["onClick","severity"])),64))])])]),_:1}),r("div",me,[(o(!0),n(h,null,C(L(),(i,w)=>(o(),D(f,{key:"comb-"+w},{title:a(()=>[S("Combination "+I(w+1),1)]),content:a(()=>[r("div",ue,[(o(!0),n(h,null,C(i,(W,G)=>(o(),D(x,{key:G,value:W,severity:"secondary"},null,8,["value"]))),128))])]),_:2},1024))),128))])])]),_:1},8,["items"]),l(b,{visible:m.value,"onUpdate:visible":s[1]||(s[1]=i=>m.value=i),modal:"",closable:!1,style:{width:"300px"}},{header:a(()=>s[6]||(s[6]=[r("span",{class:"font-bold"},"Generating combinations",-1)])),footer:a(()=>s[7]||(s[7]=[r("span",{class:"text-sm text-gray-500"},"Please wait...",-1)])),default:a(()=>[r("div",fe,[l(q(j))])]),_:1},8,["visible"]),l(b,{visible:t.makeNumbersConfirm,"onUpdate:visible":s[3]||(s[3]=i=>t.makeNumbersConfirm=i),style:{width:"450px"},header:"Confirm",modal:!0},{footer:a(()=>[l(u,{label:"No",icon:"pi pi-times",text:"",onClick:s[2]||(s[2]=i=>t.makeNumbersConfirm=!1)}),l(u,{label:"Yes",icon:"pi pi-check",onClick:v})]),default:a(()=>[s[8]||(s[8]=r("div",{class:"flex items-center gap-4"},[r("i",{class:"pi pi-exclamation-triangle !text-3xl"}),r("span",null," Are you sure you want to generate magik numbers? ")],-1))]),_:1,__:[8]},8,["visible"])],64)}}};export{Te as default};
