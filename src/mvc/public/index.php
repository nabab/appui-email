<html>
  <head>
    <script>
      class Quentin2 extends HTMLElement {
        #testPrivate = 1;
        get testPublic() {
          return this.#testPrivate;
        }
        set testPublic(v) {
          console.log("Hello world");
          this.#testPrivate = v;
        }
        constructor() {
          super();
        }
        coucou() {
          console.log("coucou");
        }
        connectedCallback() {
          this.style.backgroundColor = "pink";
          this.style.width = "50px";
          this.style.height = "50px";
          this.style.display = "block";
        }
      }
      
      class Quentin3 extends HTMLElement {
        #testPrivate = 1;
        get testPublic() {
          return this.#testPrivate;
        }
        set testPublic(v) {
          console.log("Hello world");
          this.#testPrivate = v;
        }
        constructor() {
          super();
        }
        coucou() {
          console.log("coucou");
        }
        connectedCallback() {
          this.style.backgroundColor = "pink";
          this.style.width = "500px";
          this.style.height = "500px";
          this.style.display = "block";
        }
      }

      customElements.define("test-quentin", Quentin2)
      customElements.define("test-q", Quentin3)
    </script>
  </head>
  <body>
    <test-quentin>

    </test-quentin>
    <test-q>
    
    </test-q>
  </body>
</html>