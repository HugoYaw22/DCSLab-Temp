import SideMenu from "@/layouts/side-menu/Main.vue";

import Cash from "@/views/cash/Cash.vue";
import Capital from "@/views/capital/Capital.vue";
import CapitalGroup from "@/views/capitalgroup/CapitalGroup.vue";
import Expense from "@/views/expense/Expense.vue";
import ExpenseGroup from "@/views/expensegroup/ExpenseGroup.vue";
import Income from "@/views/income/Income.vue";
import IncomeGroup from "@/views/incomegroup/IncomeGroup.vue";
import Investor from "@/views/investor/Investor.vue";

const root = '/dashboard';

export default {
    path: root + '/cash',
    component: SideMenu,
    children: [
        {
            path: root + '/cash' + '/cash',
            name: 'side-menu-cash-cash',
            component: Company,
            meta: { 
                remember: true,
                log_route: true 
            }
        },
        {
            path: root + '/cash' + '/capital' + '/capital',
            name: 'side-menu-cash-capital-capital',
            component: Capital,
            meta: { 
                remember: true,
                log_route: true 
            }
        },
        {
            path: root + '/cash' + '/capital' + '/capitalgroup',
            name: 'side-menu-cash-capital-capitalgroup',
            component: CapitalGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/expense' + '/expense',
            name: 'side-menu-cash-expense',
            component: Expense,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/expense' + '/expensegroup',
            name: 'side-menu-cash-expense-expensegroup',
            component: ExpenseGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/income' + '/income',
            name: 'side-menu-cash-income-income',
            component: Income,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/income' + '/incomegroup',
            name: 'side-menu-cash-income-incomegroup',
            component: ExpenseGroup,
            meta: { 
                remember: true,
                log_route: true
            }
        },
        {
            path: root + '/cash' + '/investor' + '/investor',
            name: 'side-menu-cash-investor-investor',
            component: Investor,
            meta: {
                remember: true,
                log_route: true
            }
        },
    ]
};