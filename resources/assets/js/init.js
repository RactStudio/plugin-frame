import '@flowbite';

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { faPalette, faSun, faMoon, faDesktop } from "@fortawesome/free-solid-svg-icons";

// Add only required icons to the library
library.add(faPalette, faSun, faMoon, faDesktop);

// Automatically replace <i> tags with SVG icons
dom.watch();
