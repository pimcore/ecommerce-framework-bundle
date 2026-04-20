# E-Commerce Framework

> [!IMPORTANT]
> ## This Repository Has Been Archived
>
> This bundle has moved to the Pimcore Enterprise Edition. The GPL version is **EOL** and will no longer receive updates.
>
> - **Enterprise repository:** The updated and supported version is available at [ee-ecommerce-framework-bundle](https://github.com/pimcore/ee-ecommerce-framework-bundle) (access is granted by your Pimcore contact person when you have a valid enterprise subscription).
> - **LTS support:** For long-term support, please use our support portal at [get.support.pimcore.com](https://get.support.pimcore.com/) (access is granted by your Pimcore contact person when you have a valid enterprise subscription).
> - **Community support:** For community help and general questions, head over to [Pimcore Discussions](https://github.com/orgs/pimcore/discussions).



## Why Pimcore E-Commerce Framework
The e-commerce environment has fundamentally changed and e-commerce projects often face new challenges like: 
 - **complex product structures:** lots of data attributes, complex product relations, configurable product systems, 
    different sources for products
 - **complex pricing structures:** product dependent price sources, tier pricing, pricing matrices, individual pricing 
   rules, integration of remote pricing services
 - **complex availability calculations**
 - **thousands customer groups with:** customer group specific prices, customer group specific assortments
 - **individual checkouts:** individual integration of backend systems, integration of multi-channel processes into 
   checkout workflow
 - **individual design**
 - **highly agile projects with changing requirements:** 'fail fast', fast changing environments and requirements
 - etc. 
 
We think for these challenges a default shop system that pops out of a box, that has a fixed product data model, fixed 
workflows, a template based frontend and needs to be connected and integrated with other systems via interfaces 
is not the tool to go for. 

We think for these challenges you need...
- a **framework for developers** to build **outstanding e-commerce solutions for customers**,
- with a native integration into Pimcore,
- with a component based architecture
- and a strict separation between backend functionality and frontend presentation. 

That is the idea behind the E-Commerce Framework of Pimcore. Like Pimcore itself, it is not a ready made system,
it is a set of tools and functionality to help building e-commerce applications really fast and flexible. 

 
## Provided Functionality in a Nutshell 
- Tools for indexing, listing, searching and filtering products 
- Implementations of carts, wish lists, comparison lists, etc.
- Concepts for flexible and complex pricing, taxes and availability functionality 
- Functionality and tools for implementing checkout processes
- Pricing Rules and Vouchers
- Tools for working with and managing Orders
- Concepts for setting up multi tenant and multi shop solutions

For a first impression have a look at our [Demo](https://demo.pimcore.fun). For more complex solutions
have a look at our [case studies](https://pimcore.com/en/customers?industry=&capability=618). 


## Working With E-Commerce Framework
 
Following aspects are short-cuts into the documentation for start working with the E-Commerce Framework: 

- [Architecture Overview](./doc/01_Architecture_Overview.md)
- [Installation](./doc/03_Installation/README.md) and [Configuration](./doc/04_Configuration/README.md)
- [Indexing and Listing Products](./doc/05_Index_Service/README.md)
- [Filtering Products](./doc/07_Filter_Service/README.md)
- [Working with Prices](./doc/09_Working_with_Prices/README.md)
- [Working with Carts](./doc/11_Cart_Manager.md)
- [Setting up Checkouts](./doc/13_Checkout_Manager/README.md)
- [Integrating Payment Functionality](./doc/15_Payment/README.md)
- [Working with Orders](./doc/17_Order_Manager/README.md)
- [Tracking Manager](./doc/19_Tracking_Manager.md)
- [Events](./doc/20_Event_API_and_Event_Manager.md)
