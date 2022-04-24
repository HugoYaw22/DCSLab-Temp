import SideMenu from "@/layouts/side-menu/Main.vue";

import Customer from "@/views/customer/Customer.vue";
import CustpmerGroup from "@/views/branch/CustpmerGroup.vue";

const root = '/dashboard';

export default {
    path: root + '/customer',
    component: SideMenu,
    children: [
        {
            path: root + '/customer' + '/customer',
            name: 'side-menu-customer-customer',
            component: Customer,
            meta: { 
                remember: true,
                log_route: true 
            }
        },
        {
            path: root + '/customer' + '/customergroup',
            name: 'side-menu-customer-customergroup',
            component: CustomerGroup,
            meta: { 
                remember: true,
                log_route: true 
            }
        },
    ]
};