<style type="text/css">
    .tech-banner{background-color: #F7F5F5;}
    .tech-banner h2{color: #191919; font-size: 60px; font-style: normal; font-weight: 500; line-height: normal; letter-spacing: -3px;}
    .tech-banner p{color: #686868; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}
</style>

<div class="tech-banner">

        <!--<section class="py-10">
          <div class="container mx-auto grid grid-cols-1 lg:grid-cols-2 gap-40 items-stretch">
            
            <div class="flex items-center">
              <h2>
                The Growth Acceleration<br>
                E-Commerce Web<br>
                Design Agency
              </h2>
            </div>

            <div class="flex items-end justify-end">
              <p>
                FDRY, Foundry Digital, is a digital web design agency with over 13 years of experience primarily as e-commerce web designers. During this time, we have built a strong reputation enabling us to create brands and build their websites using Shopify, WooCommerce, WordPress, Adobe Commerce / Magento and BigCommerce technologies.
              </p>
            </div>

          </div>
        </section>-->


        <!-- Grid effect -->
          <section class="sectiongrid">
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

            <style type="text/css">

              #custom-cursor-shadow {
                  position: fixed; 
                  width: 40px; 
                  height: 40px; 
                  border-radius: 50%;
                  background-color: rgba(59, 130, 246, 0.1);
                  box-shadow: 0 0 30px 10px rgba(59, 130, 246, 0.3);
                  pointer-events: none; 
                  left: 0; 
                  top: 0; 
                  z-index: 1000;
                  
                  opacity: 0;
                  transform: scale(0);
                  
                  transition: transform 0.3s ease-out, opacity 0.3s ease-out;
              }

              #custom-cursor-shadow.hovered { 
                  opacity: 1;
                  transform: scale(1.5); 
              }

              #fake-pointer {
                  position: fixed;
                  left: 0;
                  top: 0;
                  pointer-events: none;
                  z-index: 1001;
                  width: 24px;
                  height: 24px;
              }

              #traveling-train {
                  position: absolute; 
                  height: 2px;
                  background: linear-gradient(to right, rgba(59, 130, 246, 0.0), rgb(59, 130, 246));
                  box-shadow: 0 0 5px 1px rgba(59, 130, 246, 0.7);
                  pointer-events: none; 
                  z-index: 50; 
                  opacity: 0;
                  width: 0;
              }
              .grid-item.border-active {
                  transition: box-shadow 0.3s ease-out;
              }
              .grid-item {
                  transition: box-shadow 0.3s ease-out;
                  box-shadow: inset 0 0 0 0px transparent;
              }
              #the-actual-grid {
                  border-left-color: transparent !important;
                  border-right-color: transparent !important;
                  border-top-color: transparent !important;
              }
              #the-actual-grid .grid-item:first-child {
                  
              }
              #the-actual-grid .grid-item:last-child {
                  
              }
              .svg-plus-icon {
                  color: #0000004d;
                  stroke: #0000004d;
              }
              .svgtop{
                top: 8px;
                left: 8px;
              }
              .svgbottom{
                left: 8px;
                bottom: 7px;
              }
              .svg-plus-icon {
                  color: #ccc;
                  stroke: #ccc;
                  transition: color 0.3s ease-out; 
              }
              .grid-item.border-active .svg-plus-icon {
                  color: #4951F2;
                  stroke: #4951F2;
              }
              .grid-item.border-active + .grid-item .svg-plus-icon {
                  color: #4951F2;
                  stroke: #4951F2;
              }
              .grid-item[data-row="2"][data-col="0"] {
                  border-left-color: transparent !important;
                  border-bottom-color: transparent !important;
              }
              .grid-item[data-row="2"]{
                  border-bottom-color: transparent !important;
              }
              .grid-item[data-row="1"][data-col="0"] {
                  border-left-color: transparent !important;
              }
              .grid-item[data-row="0"]{
                 border-top-color: transparent !important;
              }
              .grid-item img{width: 85%;}

              .mobileicon{display: none;}

              @media (max-width: 767px) {
                  .grid-item[data-row="1"][data-col="0"],
                  .grid-item[data-row="1"][data-col="7"] {
                      display: none !important;
                  }

                  .grid-item[data-row="0"],
                  .grid-item[data-row="2"] {
                       display: none !important;
                  }

                  .mobileicon{display: block;}
              }
              @media (max-width: 768px) { /* Corresponde a 'md:' de Tailwind */
                .grid-item[data-row="1"][data-col="1"],.grid-item[data-row="1"][data-col="2"]{
                  border-top: 1px solid rgb(229 231 235 / var(--tw-divide-opacity, 1));
                }
                .grid-item[data-row="1"][data-col="2"],
                .grid-item[data-row="1"][data-col="4"],
                .grid-item[data-row="1"][data-col="6"]
                {
                  border-right: 1px solid rgb(229 231 235 / var(--tw-divide-opacity, 1));
                }


                #the-actual-grid {
                    border-left-color: transparent !important;
                    border-right-color: transparent !important;
                    border-top-color: transparent !important;
                }
                .grid-item[data-row="2"] { /* Oculta borde inferior de la última fila desktop */
                    border-bottom-color: transparent !important;
                }
                .grid-item[data-row="0"]:not([data-col="0"]),
                .grid-item[data-row="2"]:not([data-col="0"]) { /* Oculta división-x en filas fantasma desktop */
                    border-left-color: transparent !important;
                }
                #the-actual-grid .grid-item[data-col="0"],
                #the-actual-grid .grid-item[data-col="7"] { /* Oculta borde inferior de cols fantasma desktop */
                    border-bottom-color: transparent !important;
                }
                #the-actual-grid .grid-item[data-col="7"] { /* Oculta división-x de última col fantasma desktop */
                     border-left-color: transparent !important;
                }
                .grid-item[data-row="2"][data-col="0"] { /* Oculta borde izq/inf de celda [2,0] desktop */
                    border-left-color: transparent !important;
                    /* border-bottom-color ya está cubierto por [data-row="2"] */
                }
            }
            </style>

            <div class="py-10 flex items-center justify-center">

              <div id="custom-cursor-shadow"></div>
          
              <div id="grid-container" class="w-full relative">
                  <div id="traveling-train"></div> 

                  <div id="the-actual-grid" class="grid grid-cols-2 md:grid-cols-8 divide-x md:divide-x divide-gray-200 border-t md:border-l md:border-r border-gray-200">
                  
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="0">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="1">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="2">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="3">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="4">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="5">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="6">
                    </div>
                    <div class="grid-item relative h-48 border-b hidden md:block" data-row="0" data-col="7">
                    </div>

                    <div class="grid-item relative h-48 border-b border-gray-200" data-row="1" data-col="0">
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="1">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/shopify.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="2">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/woocommerce.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40 mobileicon" style="top: -8px; right: -8px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="3">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/meta.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="4">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/google-partner.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40 mobileicon" style="top: -8px; right: -8px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="5">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/big-commerce.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative flex justify-center items-center h-48 border-b border-gray-200" data-row="1" data-col="6">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/img/square.svg" />
                        <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40 mobileicon" style="top: -8px; right: -8px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                        <svg class="svg-plus-icon absolute z-40 mobileicon" style="bottom: -8px; right: -8px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                        </svg>
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200" data-row="1" data-col="7">
                      <svg class="svg-plus-icon absolute z-40" style="top: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                          <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                      </svg>
                      <svg class="svg-plus-icon absolute z-40" style="bottom: -8px; left: -9px;" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                          <path d="M7.43605 0V14.8721M0 7.43605H14.8721" stroke-width="1.93788"/>
                      </svg>
                    </div>

                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="0">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="1">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="2">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="3">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="4">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="5">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="6">
                    </div>
                    <div class="grid-item relative h-48 border-b border-gray-200 hidden md:block" data-row="2" data-col="7">
                    </div>
                </div>
              </div>


              <script>
                window.addEventListener("load", () => {

                    // --- 1. SELECCIONAR ELEMENTOS ---
                    const customCursor = document.getElementById('custom-cursor-shadow');
                    const fakePointer = document.getElementById('fake-pointer');
                    const line = document.getElementById('traveling-train');
                    const gridContainer = document.getElementById('grid-container');
                    let gridItems = document.querySelectorAll('.grid-item:not([data-row="0"]):not([data-row="2"]):not([data-col="0"]):not([data-col="7"])'); // Interactivos

                    const numColsDesktop = 8;
                    let cellPositions = [];
                    let activeTimeline = null;

                    // --- 2. LÓGICA DEL CURSOR ---
                    let mouse = { x: 0, y: 0 };
                    let shadowPos = { x: 0, y: 0 };
                    const speed = 0.2;

                    window.addEventListener('mousemove', (e) => {
                        mouse.x = e.clientX;
                        mouse.y = e.clientY;
                    });

                    gsap.ticker.add(() => {
                        shadowPos.x += (mouse.x - shadowPos.x) * speed;
                        shadowPos.y += (mouse.y - shadowPos.y) * speed;
                        gsap.set(customCursor, { x: shadowPos.x - 20, y: shadowPos.y - 20 });
                        gsap.set(fakePointer, { x: mouse.x, y: mouse.y });
                    });

                    // --- 3. FUNCIÓN PARA CALCULAR POSICIONES ---
                    function calculatePositions() {
                        const containerRect = gridContainer.getBoundingClientRect();
                        cellPositions = [];
                        const allGridItems = document.querySelectorAll('.grid-item');

                        allGridItems.forEach(item => {
                            const row = parseInt(item.dataset.row);
                            const col = parseInt(item.dataset.col);
                            const itemRect = item.getBoundingClientRect();
                            if (!cellPositions[row]) cellPositions[row] = [];
                            cellPositions[row][col] = {
                                x_left: itemRect.left - containerRect.left,
                                x_right: itemRect.right - containerRect.left,
                                y_top: itemRect.top - containerRect.top
                            };
                        });
                        addEventListenersToItems();
                    }

                    let resizeTimer;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(calculatePositions, 250);
                    });

                    function addEventListenersToItems() {
                         document.querySelectorAll('.grid-item').forEach(item => {
                            item.replaceWith(item.cloneNode(true));
                         });
                         gridItems = document.querySelectorAll('.grid-item:not([data-row="0"]):not([data-row="2"]):not([data-col="0"]):not([data-col="7"])');

                         gridItems.forEach(item => {
                            item.addEventListener('mouseenter', () => {

                                const isMobile = window.innerWidth < 768;
                                if (isMobile) {
                                    return;
                                }

                                if (activeTimeline) {
                                    activeTimeline.kill();
                                    activeTimeline = null;
                                }

                                customCursor.classList.add('hovered');
                                document.querySelectorAll('.grid-item.border-active').forEach(activeItem => {
                                    activeItem.classList.remove('border-active');
                                });

                                const targetRow = parseInt(item.dataset.row);
                                const targetCol = parseInt(item.dataset.col);

                                if (targetRow !== 1 || targetCol === 0 || targetCol === (numColsDesktop - 1)) {
                                     customCursor.classList.remove('hovered');
                                     return;
                                }

                                customCursor.classList.add('hovered');
                                const targetY = cellPositions[targetRow][targetCol].y_top - 1;
                                let startX, targetX, gradient;
                                activeTimeline = gsap.timeline({ onComplete: () => { activeTimeline = null; } });

                                // Lógica Desktop (ya la tenías)
                                if (targetCol <= 3) {
                                     startX = cellPositions[targetRow][0].x_left;
                                     targetX = cellPositions[targetRow][targetCol].x_left;
                                     gradient = 'linear-gradient(to right, rgba(59, 130, 246, 0.1), rgb(59, 130, 246))';
                                     activeTimeline.set(line, { x: startX, y: targetY, width: 0, scaleX: 1, opacity: 1, background: gradient, transformOrigin: 'left center' });
                                     activeTimeline.to(line, { width: targetX - startX, duration: 0.4, ease: "power2.out" });
                                     activeTimeline.call(() => item.classList.add('border-active'));
                                     activeTimeline.set(line, { transformOrigin: 'right center' });
                                     activeTimeline.to(line, { scaleX: 0, opacity: 0.5, duration: 0.3, ease: "power2.in" });
                                } else {
                                     startX = cellPositions[targetRow][numColsDesktop - 1].x_right;
                                     targetX = cellPositions[targetRow][targetCol].x_right;
                                     gradient = 'linear-gradient(to left, rgba(59, 130, 246, 0.1), rgb(59, 130, 246))';
                                     activeTimeline.set(line, { x: startX, y: targetY, width: 0, scaleX: 1, opacity: 1, background: gradient, transformOrigin: 'left center' });
                                     activeTimeline.to(line, { x: targetX, width: startX - targetX, duration: 0.4, ease: "power2.out" });
                                     activeTimeline.call(() => item.classList.add('border-active'));
                                     activeTimeline.to(line, { scaleX: 0, opacity: 0.5, duration: 0.3, ease: "power2.in" });
                                }
                            }); // Fin mouseenter

                            item.addEventListener('mouseleave', () => {
                                customCursor.classList.remove('hovered');
                            });
                        });
                    }


                    gridContainer.addEventListener('mouseleave', () => {
                        if (activeTimeline) {
                            activeTimeline.kill();
                            activeTimeline = null;
                        }
                        gsap.set(line, { opacity: 0, width: 0 });
                        document.querySelectorAll('.grid-item.border-active').forEach(activeItem => {
                            activeItem.classList.remove('border-active');
                        });
                        customCursor.classList.remove('hovered');
                    });

                    calculatePositions();

                });
            </script>

            </div>
          </section>
          <!-- End Grid effect -->

    </div>