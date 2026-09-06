import accountCashOutlineSvg from '@mdi/svg/svg/account-cash-outline.svg?raw';
import accountEditOutlineSvg from '@mdi/svg/svg/account-edit-outline.svg?raw';
import accountGroupOutlineSvg from '@mdi/svg/svg/account-group-outline.svg?raw';
import accountOffOutlineSvg from '@mdi/svg/svg/account-off-outline.svg?raw';
import accountPlusOutlineSvg from '@mdi/svg/svg/account-plus-outline.svg?raw';
import bellOutlineSvg from '@mdi/svg/svg/bell-outline.svg?raw';
import calendarCheckOutlineSvg from '@mdi/svg/svg/calendar-check-outline.svg?raw';
import calendarMonthOutlineSvg from '@mdi/svg/svg/calendar-month-outline.svg?raw';
import calendarRangeOutlineSvg from '@mdi/svg/svg/calendar-range-outline.svg?raw';
import calendarSearchOutlineSvg from '@mdi/svg/svg/calendar-search-outline.svg?raw';
import cashPlusSvg from '@mdi/svg/svg/cash-plus.svg?raw';
import chartBarSvg from '@mdi/svg/svg/chart-bar.svg?raw';
import chartBoxOutlineSvg from '@mdi/svg/svg/chart-box-outline.svg?raw';
import chevronDownSvg from '@mdi/svg/svg/chevron-down.svg?raw';
import eyeOffOutlineSvg from '@mdi/svg/svg/eye-off-outline.svg?raw';
import eyeOutlineSvg from '@mdi/svg/svg/eye-outline.svg?raw';
import fileDocumentOutlineSvg from '@mdi/svg/svg/file-document-outline.svg?raw';
import fileDocumentPlusOutlineSvg from '@mdi/svg/svg/file-document-plus-outline.svg?raw';
import filePlusOutlineSvg from '@mdi/svg/svg/file-plus-outline.svg?raw';
import filterOutlineSvg from '@mdi/svg/svg/filter-outline.svg?raw';
import formatListBulletedSvg from '@mdi/svg/svg/format-list-bulleted.svg?raw';
import homeOutlineSvg from '@mdi/svg/svg/home-outline.svg?raw';
import lockResetSvg from '@mdi/svg/svg/lock-reset.svg?raw';
import logoutSvg from '@mdi/svg/svg/logout.svg?raw';
import pencilOutlineSvg from '@mdi/svg/svg/pencil-outline.svg?raw';
import plusSvg from '@mdi/svg/svg/plus.svg?raw';
import tagOutlineSvg from '@mdi/svg/svg/tag-outline.svg?raw';
import tagPlusOutlineSvg from '@mdi/svg/svg/tag-plus-outline.svg?raw';
import targetSvg from '@mdi/svg/svg/target.svg?raw';
import trashCanOutlineSvg from '@mdi/svg/svg/trash-can-outline.svg?raw';
import trendingDownSvg from '@mdi/svg/svg/trending-down.svg?raw';
import trendingUpSvg from '@mdi/svg/svg/trending-up.svg?raw';
import walletOutlineSvg from '@mdi/svg/svg/wallet-outline.svg?raw';

function svgPath(svg) {
    return svg.match(/<path[^>]*d="([^"]+)"/)?.[1] ?? '';
}

export const icons = {
    accountCashOutline: svgPath(accountCashOutlineSvg),
    accountEditOutline: svgPath(accountEditOutlineSvg),
    accountGroupOutline: svgPath(accountGroupOutlineSvg),
    accountOffOutline: svgPath(accountOffOutlineSvg),
    accountPlusOutline: svgPath(accountPlusOutlineSvg),
    bellOutline: svgPath(bellOutlineSvg),
    calendarCheckOutline: svgPath(calendarCheckOutlineSvg),
    calendarMonthOutline: svgPath(calendarMonthOutlineSvg),
    calendarRangeOutline: svgPath(calendarRangeOutlineSvg),
    calendarSearchOutline: svgPath(calendarSearchOutlineSvg),
    cashPlus: svgPath(cashPlusSvg),
    chartBar: svgPath(chartBarSvg),
    chartBoxOutline: svgPath(chartBoxOutlineSvg),
    chevronDown: svgPath(chevronDownSvg),
    eyeOffOutline: svgPath(eyeOffOutlineSvg),
    eyeOutline: svgPath(eyeOutlineSvg),
    fileDocumentOutline: svgPath(fileDocumentOutlineSvg),
    fileDocumentPlusOutline: svgPath(fileDocumentPlusOutlineSvg),
    filePlusOutline: svgPath(filePlusOutlineSvg),
    filterOutline: svgPath(filterOutlineSvg),
    formatListBulleted: svgPath(formatListBulletedSvg),
    homeOutline: svgPath(homeOutlineSvg),
    lockReset: svgPath(lockResetSvg),
    logout: svgPath(logoutSvg),
    pencilOutline: svgPath(pencilOutlineSvg),
    plus: svgPath(plusSvg),
    tagOutline: svgPath(tagOutlineSvg),
    tagPlusOutline: svgPath(tagPlusOutlineSvg),
    target: svgPath(targetSvg),
    trashCanOutline: svgPath(trashCanOutlineSvg),
    trendingDown: svgPath(trendingDownSvg),
    trendingUp: svgPath(trendingUpSvg),
    walletOutline: svgPath(walletOutlineSvg),
};
