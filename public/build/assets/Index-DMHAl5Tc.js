import{E as v,i as y,B as k,s as w,c as d,b as l,g as T,e as s,m as a,t as i,F as x,j as M,k as L,v as S,r as B,o as C,l as V,h as K,p as U,w as $,d as D}from"./app-Cxz8kPVe.js";import{_ as E}from"./AppLayout-Bl-vab2g.js";import"./index-BGeYvXUX.js";import"./DropdownLink-DAhIJGaX.js";import"./index-BDd8EV_O.js";import"./index-oCGvJJos.js";import"./index-DywEHISW.js";var m=v(),H=y`
    .p-terminal {
        height: dt('terminal.height');
        overflow: auto;
        background: dt('terminal.background');
        color: dt('terminal.color');
        border: 1px solid dt('terminal.border.color');
        padding: dt('terminal.padding');
        border-radius: dt('terminal.border.radius');
    }

    .p-terminal-prompt {
        display: flex;
        align-items: center;
    }

    .p-terminal-prompt-value {
        flex: 1 1 auto;
        border: 0 none;
        background: transparent;
        color: inherit;
        padding: 0;
        outline: 0 none;
        font-family: inherit;
        font-feature-settings: inherit;
        font-size: 1rem;
    }

    .p-terminal-prompt-label {
        margin-inline-end: dt('terminal.prompt.gap');
    }

    .p-terminal-input::-ms-clear {
        display: none;
    }

    .p-terminal-command-response {
        margin: dt('terminal.command.response.margin');
    }
`,I={root:"p-terminal p-component",welcomeMessage:"p-terminal-welcome-message",commandList:"p-terminal-command-list",command:"p-terminal-command",commandValue:"p-terminal-command-value",commandResponse:"p-terminal-command-response",prompt:"p-terminal-prompt",promptLabel:"p-terminal-prompt-label",promptValue:"p-terminal-prompt-value"},N=k.extend({name:"terminal",style:H,classes:I}),A={name:"BaseTerminal",extends:w,props:{welcomeMessage:{type:String,default:null},prompt:{type:String,default:null}},style:N,provide:function(){return{$pcTerminal:this,$parentInstance:this}}},b={name:"Terminal",extends:A,inheritAttrs:!1,data:function(){return{commandText:null,commands:[]}},mounted:function(){m.on("response",this.responseListener),this.$refs.input.focus()},updated:function(){this.$el.scrollTop=this.$el.scrollHeight},beforeUnmount:function(){m.off("response",this.responseListener)},methods:{onClick:function(){this.$refs.input.focus()},onKeydown:function(t){t.key==="Enter"&&this.commandText&&(this.commands.push({text:this.commandText}),m.emit("command",this.commandText),this.commandText="")},responseListener:function(t){this.commands[this.commands.length-1].response=t}}};function F(e,t,u,c,o,n){return l(),d("div",a({class:e.cx("root"),onClick:t[2]||(t[2]=function(){return n.onClick&&n.onClick.apply(n,arguments)})},e.ptmi("root")),[e.welcomeMessage?(l(),d("div",a({key:0,class:e.cx("welcomeMessage")},e.ptm("welcomeMessage")),i(e.welcomeMessage),17)):T("",!0),s("div",a({class:e.cx("commandList")},e.ptm("content")),[(l(!0),d(x,null,M(o.commands,function(r,p){return l(),d("div",a({key:r.text+p.toString(),class:e.cx("command")},{ref_for:!0},e.ptm("commands")),[s("span",a({class:e.cx("promptLabel")},{ref_for:!0},e.ptm("prompt")),i(e.prompt),17),s("span",a({class:e.cx("commandValue")},{ref_for:!0},e.ptm("command")),i(r.text),17),s("div",a({class:e.cx("commandResponse"),"aria-live":"polite"},{ref_for:!0},e.ptm("response")),i(r.response),17)],16)}),128))],16),s("div",a({class:e.cx("prompt")},e.ptm("container")),[s("span",a({class:e.cx("promptLabel")},e.ptm("prompt")),i(e.prompt),17),L(s("input",a({ref:"input","onUpdate:modelValue":t[0]||(t[0]=function(r){return o.commandText=r}),class:e.cx("promptValue"),type:"text",autocomplete:"off",onKeydown:t[1]||(t[1]=function(){return n.onKeydown&&n.onKeydown.apply(n,arguments)})},e.ptm("commandText")),null,16),[[S,o.commandText]])],16)],16)}b.render=F;const G={__name:"Index",props:{title:String},setup(e){const u=B([{label:"Admin"},{label:e.title,url:route("admin.terminal.index")}]);C(()=>{m.on("command",c)}),V(()=>{m.off("command",c)});const c=async o=>{var f,g;let n,r=o.indexOf(" "),p=r!==-1?o.substring(0,r):o;switch(p){case"date":n="Today is "+new Date().toDateString();break;case"greet":n="Hola "+o.substring(r+1);break;case"random":n=Math.floor(Math.random()*100);break;case"artisan":try{n=(await K.post("/api/terminal/command",{command:args||"lottery:generate-magik-numbers"})).data.output}catch(h){n=((g=(f=h.response)==null?void 0:f.data)==null?void 0:g.output)||"Comando fallido"}break;default:n="Unknown command: "+p}m.emit("response",n)};return(o,n)=>{const r=b;return l(),U(E,{items:u.value},{default:$(()=>[D(r,{welcomeMessage:"Welcome to terminal",prompt:"$","aria-label":"Terminal Service"})]),_:1},8,["items"])}}};export{G as default};
